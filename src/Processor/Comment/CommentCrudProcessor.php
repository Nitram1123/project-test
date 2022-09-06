<?php

declare(strict_types=1);

namespace App\Processor\Comment;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Member;
use App\Mutator\CommandBusInterface;
use App\Mutator\Comment\CreateCommentCommand;
use App\Mutator\Comment\DeleteCommentCommand;
use App\Mutator\Comment\UpdateCommentCommand;
use App\Processor\ProcessorInterface;
use App\Query\Article\FindArticleQuery;
use App\Query\Comment\FindCommentQuery;
use App\Query\QueryBusInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;

final class CommentCrudProcessor implements ProcessorInterface
{
    public function __construct(
        private SerializerInterface $serializer,
        private CommandBusInterface $commandBus,
        private QueryBusInterface $queryBus,
        private ValidatorInterface $validator,
        private TokenStorageInterface $tokenStorage,
        private AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    public function process(mixed $data, string $operation, int|string|null $id = null): mixed
    {
        if ($operation === ProcessorInterface::CREATE) {
            $author = $this->tokenStorage->getToken()->getUser();
            \assert($author instanceof Member);
            $command = $this->serializer->deserialize($data, CreateCommentCommand::class, 'json');
            \assert($command instanceof CreateCommentCommand);
            $command->authorId = (string) $author->getId();
            $this->validator->validate($command);
            if ($command->parentId !== null) {
                $parent = $this->queryBus->ask(new FindCommentQuery($command->parentId));
                if ($parent === null) {
                    throw new NotFoundHttpException('Comment not found.');
                }
            }

            if ($command->articleId !== null) {
                $article = $this->queryBus->ask(new FindArticleQuery($command->articleId));
                if ($article === null) {
                    throw new NotFoundHttpException('Article not found.');
                }
            }

            return $this->commandBus->dispatch($command);
        }

        if (($operation !== ProcessorInterface::UPDATE && $operation !== ProcessorInterface::DELETE) || $id === null) {
            throw new \LogicException();
        }

        $comment = $this->queryBus->ask(new FindCommentQuery((string) $id));
        if ($comment === null) {
            throw new NotFoundHttpException('Comment not found.');
        }

        if ($operation === ProcessorInterface::DELETE) {
            $this->denyAccessUnlessGranted('COMMENT_DELETE', $comment);

            return $this->commandBus->dispatch(new DeleteCommentCommand((string) $id));
        }

        $this->denyAccessUnlessGranted('COMMENT_EDIT', $comment);
        $command     = $this->serializer->deserialize($data, UpdateCommentCommand::class, 'json');
        $command->id = (string) $id;
        if ($command->enabled !== null) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        return $this->commandBus->dispatch($command);
    }

    private function isGranted(string $attribute, object|null $subject = null): bool
    {
        return $this->authorizationChecker->isGranted($attribute, $subject);
    }

    private function denyAccessUnlessGranted(
        string $attribute,
        object|null $subject = null,
        string $message = 'Access Denied.',
    ): void {
        if (! $this->isGranted($attribute, $subject)) {
            $exception = new AccessDeniedException($message);
            $exception->setAttributes($attribute);
            $exception->setSubject($subject);

            throw $exception;
        }
    }
}

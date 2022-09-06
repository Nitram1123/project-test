<?php

declare(strict_types=1);

namespace App\Processor\Article;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Member;
use App\Mutator\Article\CreateArticleCommand;
use App\Mutator\Article\DeleteArticleCommand;
use App\Mutator\Article\UpdateArticleCommand;
use App\Mutator\CommandBusInterface;
use App\Processor\ProcessorInterface;
use App\Query\Article\FindArticleQuery;
use App\Query\QueryBusInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class ArticleCrudProcessor implements ProcessorInterface
{
    public function __construct(
        private SerializerInterface $serializer,
        private CommandBusInterface $commandBus,
        private QueryBusInterface $queryBus,
        private ValidatorInterface $validator,
        private TokenStorageInterface $tokenStorage,
    ) {
    }

    public function process(mixed $data, string $operation, int|string|null $id = null): mixed
    {
        if ($operation === ProcessorInterface::CREATE) {
            $author = $this->tokenStorage->getToken()->getUser();
            \assert($author instanceof Member);
            $command = $this->serializer->deserialize($data, CreateArticleCommand::class, 'json');
            \assert($command instanceof CreateArticleCommand);
            $command->authorId = (string) $author->getId();
            $this->validator->validate($command);

            return $this->commandBus->dispatch($command);
        }

        if (($operation !== ProcessorInterface::UPDATE && $operation !== ProcessorInterface::DELETE) || $id === null) {
            throw new \LogicException();
        }

        $article = $this->queryBus->ask(new FindArticleQuery((string) $id));
        if ($article === null) {
            throw new NotFoundHttpException('Article not found.');
        }

        if ($operation === ProcessorInterface::DELETE) {
            return $this->commandBus->dispatch(new DeleteArticleCommand((string) $id));
        }

        $command     = $this->serializer->deserialize($data, UpdateArticleCommand::class, 'json');
        $command->id = (string) $id;

        return $this->commandBus->dispatch($command);
    }
}

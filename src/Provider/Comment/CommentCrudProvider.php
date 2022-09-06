<?php

declare(strict_types=1);

namespace App\Provider\Comment;

use App\Provider\ProviderInterface;
use App\Query\Comment\FindCommentQuery;
use App\Query\Comment\FindCommentsQuery;
use App\Query\QueryBusInterface;
use App\Query\QueryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class CommentCrudProvider implements ProviderInterface
{
    public function __construct(
        private QueryBusInterface $queryBus,
    ) {
    }

    public function provide(mixed $data, string $operation, int|string|null $id = null): mixed
    {
        if ($operation === ProviderInterface::READ && $id !== null) {
            $comment = $this->queryBus->ask(new FindCommentQuery((string) $id));
            if ($comment === null) {
                throw new NotFoundHttpException('Comment not found.');
            }

            return $comment;
        }

        $query = new FindCommentsQuery(
            $data['article'] ?? null,
            $data['parent'] ?? null,
            (int) ($data['page'] ?? 1),
            (int) ($data['numberByPage'] ?? QueryInterface::NUMBER_BY_PAGE),
        );

        return $this->queryBus->ask($query);
    }
}

<?php

declare(strict_types=1);

namespace App\Query\Comment;

use App\Entity\Comment;
use App\Query\QueryHandlerInterface;
use App\Repository\CommentRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class FindCommentQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
    ) {
    }

    public function __invoke(FindCommentQuery $query): Comment|null
    {
        return $this->commentRepository->searchWithId($query->id);
    }
}

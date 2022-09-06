<?php

declare(strict_types=1);

namespace App\Query\Comment;

use App\Paginator\PaginatorDTO;
use App\Paginator\PaginatorDTOInterface;
use App\Query\QueryHandlerInterface;
use App\Repository\CommentRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class FindCommentsQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
    ) {
    }

    public function __invoke(FindCommentsQuery $query): PaginatorDTOInterface
    {
        $paginatorDTO = new PaginatorDTO($query->page, $query->numberByPage);
        $this->commentRepository->search($paginatorDTO, $query->article, $query->parent);

        return $paginatorDTO;
    }
}

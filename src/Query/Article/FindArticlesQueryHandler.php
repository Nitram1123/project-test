<?php

declare(strict_types=1);

namespace App\Query\Article;

use App\Paginator\PaginatorDTO;
use App\Paginator\PaginatorDTOInterface;
use App\Query\QueryHandlerInterface;
use App\Repository\ArticleRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class FindArticlesQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private ArticleRepositoryInterface $articleRepository,
    ) {
    }

    public function __invoke(FindArticlesQuery $query): PaginatorDTOInterface
    {
        $paginatorDTO = new PaginatorDTO($query->page, $query->numberByPage);
        $this->articleRepository->search($paginatorDTO);

        return $paginatorDTO;
    }
}

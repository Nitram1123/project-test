<?php

declare(strict_types=1);

namespace App\Query\Article;

use App\Entity\Article;
use App\Query\QueryHandlerInterface;
use App\Repository\ArticleRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class FindArticleQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private ArticleRepositoryInterface $articleRepository,
    ) {
    }

    public function __invoke(FindArticleQuery $query): Article|null
    {
        return $this->articleRepository->searchWithId($query->id);
    }
}

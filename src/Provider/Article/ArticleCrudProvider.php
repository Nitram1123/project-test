<?php

declare(strict_types=1);

namespace App\Provider\Article;

use App\Provider\ProviderInterface;
use App\Query\Article\FindArticleQuery;
use App\Query\Article\FindArticlesQuery;
use App\Query\QueryBusInterface;
use App\Query\QueryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ArticleCrudProvider implements ProviderInterface
{
    public function __construct(
        private QueryBusInterface $queryBus,
    ) {
    }

    public function provide(mixed $data, string $operation, int|string|null $id = null): mixed
    {
        if ($operation === ProviderInterface::READ && $id !== null) {
            $article = $this->queryBus->ask(new FindArticleQuery((string) $id));
            if ($article === null) {
                throw new NotFoundHttpException('Article not found.');
            }

            return $article;
        }

        $query = new FindArticlesQuery(
            (int) ($data['page'] ?? 1),
            (int) ($data['numberByPage'] ?? QueryInterface::NUMBER_BY_PAGE),
        );

        return $this->queryBus->ask($query);
    }
}

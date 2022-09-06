<?php

declare(strict_types=1);

namespace App\Query\Article;

use App\Query\QueryInterface;

final class FindArticlesQuery implements QueryInterface
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $numberByPage = 10,
    ) {
    }
}

<?php

declare(strict_types=1);

namespace App\Query\Article;

use App\Query\QueryInterface;

final class FindArticleQuery implements QueryInterface
{
    public function __construct(
        public readonly string $id,
    ) {
    }
}

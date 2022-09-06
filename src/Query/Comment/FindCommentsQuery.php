<?php

declare(strict_types=1);

namespace App\Query\Comment;

use App\Query\QueryInterface;

final class FindCommentsQuery implements QueryInterface
{
    public function __construct(
        public readonly string|null $article,
        public readonly string|null $parent,
        public readonly int $page = 1,
        public readonly int $numberByPage = 10,
    ) {
    }
}

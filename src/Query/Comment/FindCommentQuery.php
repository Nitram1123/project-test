<?php

declare(strict_types=1);

namespace App\Query\Comment;

use App\Query\QueryInterface;

final class FindCommentQuery implements QueryInterface
{
    public function __construct(
        public readonly string $id,
    ) {
    }
}

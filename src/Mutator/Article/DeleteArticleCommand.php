<?php

declare(strict_types=1);

namespace App\Mutator\Article;

use App\Mutator\CommandInterface;

final class DeleteArticleCommand implements CommandInterface
{
    public function __construct(
        public readonly string $id,
    ) {
    }
}

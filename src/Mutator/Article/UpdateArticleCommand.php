<?php

declare(strict_types=1);

namespace App\Mutator\Article;

use App\Mutator\CommandInterface;

final class UpdateArticleCommand implements CommandInterface
{
    public function __construct(
        public readonly string|null $title,
        public readonly string|null $content,
        public readonly bool|null $published = null,
        public string|null $id = null,
    ) {
    }
}

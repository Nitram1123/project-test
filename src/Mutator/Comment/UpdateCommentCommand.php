<?php

declare(strict_types=1);

namespace App\Mutator\Comment;

use App\Mutator\CommandInterface;

final class UpdateCommentCommand implements CommandInterface
{
    public function __construct(
        public readonly string|null $content,
        public readonly bool|null $enabled = null,
        public string|null $id = null,
    ) {
    }
}

<?php

declare(strict_types=1);

namespace App\Mutator\Comment;

use App\Mutator\CommandInterface;

final class DeleteCommentCommand implements CommandInterface
{
    public function __construct(
        public readonly string $id,
    ) {
    }
}

<?php

declare(strict_types=1);

namespace App\Mutator\Comment;

use App\Mutator\CommandInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateCommentCommand implements CommandInterface
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string|null $content,
        #[Assert\Expression(
            'this.articleId != null || this.parentId != null',
            'A comment should be linked to an article or an other comment.'
        )]
        #[Assert\Expression(
            'this.articleId == null || this.parentId == null',
            'A comment should not be linked to an article and a comment.'
        )]
        public string|null $parentId,
        public string|null $articleId,
        public string|null $authorId = null,
    ) {
    }
}

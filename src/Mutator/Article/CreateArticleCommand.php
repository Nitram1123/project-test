<?php

declare(strict_types=1);

namespace App\Mutator\Article;

use App\Mutator\CommandInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateArticleCommand implements CommandInterface
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string|null $title,
        #[Assert\NotBlank]
        public readonly string|null $content,
        public readonly bool $published = false,
        public string|null $authorId = null,
    ) {
    }
}

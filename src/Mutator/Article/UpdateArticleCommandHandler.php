<?php

declare(strict_types=1);

namespace App\Mutator\Article;

use App\Entity\Article;
use App\Mutator\CommandHandlerInterface;
use App\Repository\ArticleRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class UpdateArticleCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private ArticleRepositoryInterface $articleRepository,
    ) {
    }

    public function __invoke(UpdateArticleCommand $command): Article
    {
        $article = $this->articleRepository->searchWithId($command->id);
        \assert($article !== null);
        if ($command->title !== null) {
            $article->setTitle($command->title);
        }

        if ($command->content !== null) {
            $article->setContent($command->content);
        }

        if ($command->published !== null) {
            $article->setPublished($command->published);
        }

        $this->articleRepository->add($article, true);

        return $article;
    }
}

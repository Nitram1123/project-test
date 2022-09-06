<?php

declare(strict_types=1);

namespace App\Mutator\Article;

use App\Mutator\CommandHandlerInterface;
use App\Repository\ArticleRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class DeleteArticleCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private ArticleRepositoryInterface $articleRepository,
    ) {
    }

    public function __invoke(DeleteArticleCommand $command): void
    {
        $article = $this->articleRepository->searchWithId($command->id);
        \assert($article !== null);
        $this->articleRepository->remove($article, true);
    }
}

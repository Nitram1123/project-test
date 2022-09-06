<?php

declare(strict_types=1);

namespace App\Mutator\Article;

use App\Entity\Article;
use App\Mutator\CommandHandlerInterface;
use App\Repository\ArticleRepositoryInterface;
use App\Repository\MemberRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateArticleCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private MemberRepositoryInterface $memberRepository,
        private ArticleRepositoryInterface $articleRepository,
    ) {
    }

    public function __invoke(CreateArticleCommand $command): Article
    {
        $article = new Article(
            $command->title,
            $command->content,
            $this->memberRepository->searchWithId($command->authorId),
        );

        $article->setPublished($command->published);
        $this->articleRepository->add($article, true);

        return $article;
    }
}

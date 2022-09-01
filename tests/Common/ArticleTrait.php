<?php

declare(strict_types=1);

namespace App\Tests\Common;

use App\Entity\Article;
use App\Entity\Comment;
use App\Repository\ArticleRepository;
use App\Repository\MemberRepository;

trait ArticleTrait
{
    /** @var array<int, Article> */
    protected array $articles = [];

    protected function getFirstArticleIri(): string
    {
        return $this->findIriBy(Article::class, ['id' => $this->articles[0]->getId()]);
    }

    protected function initializeArticles(): void
    {
        $memberRepository  = static::getContainer()->get(MemberRepository::class);
        $articleRepository = static::getContainer()->get(ArticleRepository::class);
        $bob               = $memberRepository->findOneBy(['username' => 'Bob']);
        $article           = new Article();
        $article
            ->setTitle('The best article in the world')
            ->setContent('The best article in the world')
            ->setAuthor($bob);

        for ($i = 0; $i < 10; $i++) {
            $article->addComment(
                (new Comment())
                    ->setContent('Comment-' . ($i + 1))
                    ->setAuthor($bob)
            );
        }

        $articleRepository->add($article, true);
        $this->articles[] = $article;
    }

    protected function removeArticles(): void
    {
        $articleRepository = static::getContainer()->get(ArticleRepository::class);
        $articleRepository
            ->createQueryBuilder('a')
            ->delete()
            ->getQuery()
            ->execute();
    }
}

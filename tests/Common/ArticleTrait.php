<?php

declare(strict_types=1);

namespace App\Tests\Common;

use App\Entity\Article;
use App\Entity\Comment;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
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
        for ($i = 0; $i < 15; $i++) {
            $article = new Article(
                'The best article in the world ' . ($i + 1),
                'The best article in the world ' . ($i + 1),
                $bob,
            );

            for ($j = 0; $j < 10; $j++) {
                $article->addComment(
                    (new Comment())
                        ->setContent('Comment-' . ($j + 1))
                        ->setAuthor($bob)
                );
            }

            $articleRepository->add($article, true);
            $this->articles[] = $article;
        }
    }

    protected function removeArticles(): void
    {
        $commentRepository = static::getContainer()->get(CommentRepository::class);
        $articleRepository = static::getContainer()->get(ArticleRepository::class);
        $commentRepository
            ->createQueryBuilder('c')
            ->delete()
            ->getQuery()
            ->execute();
        $articleRepository
            ->createQueryBuilder('a')
            ->delete()
            ->getQuery()
            ->execute();
    }
}

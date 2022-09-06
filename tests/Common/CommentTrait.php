<?php

declare(strict_types=1);

namespace App\Tests\Common;

use App\Entity\Article;
use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Repository\MemberRepository;
use App\Repository\NoteRepository;

trait CommentTrait
{
    /** @var array<int, Comment> */
    protected array $comments = [];

    protected function getCommentIri(int $index): string
    {
        return $this->findIriBy(Comment::class, ['id' => $this->comments[$index]->getId()]);
    }

    protected function initializeComments(): void
    {
        $memberRepository  = static::getContainer()->get(MemberRepository::class);
        $commentRepository = static::getContainer()->get(CommentRepository::class);
        $bob               = $memberRepository->findOneBy(['username' => 'Bob']);
        $alice             = $memberRepository->findOneBy(['username' => 'Alice']);
        $article           = new Article('title', 'content', $bob);

        $comment = new Comment();
        $comment
            ->setContent('The best comment in the world')
            ->setAuthor($bob)
            ->setArticle($article);

        $commentRepository->add($comment, true);
        $this->comments[] = $comment;

        $comment = new Comment();
        $comment
            ->setContent('The best comment in the world')
            ->setAuthor($alice)
            ->setArticle($article);

        for ($i = 0; $i < 5; $i++) {
            $comment->addComment(
                (new Comment())
                    ->setContent('Comment-' . ($i + 1))
                    ->setAuthor($bob)
            );
        }

        $commentRepository->add($comment, true);
        $this->comments[] = $comment;
    }

    protected function removeNotes(): void
    {
        $noteRepository = static::getContainer()->get(NoteRepository::class);
        $noteRepository
            ->createQueryBuilder('n')
            ->delete()
            ->getQuery()
            ->execute();
    }

    protected function removeComments(): void
    {
        $commentRepository = static::getContainer()->get(CommentRepository::class);
        $commentRepository
            ->createQueryBuilder('c')
            ->delete()
            ->getQuery()
            ->execute();
    }
}

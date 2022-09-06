<?php

declare(strict_types=1);

namespace App\Mutator\Comment;

use App\Entity\Comment;
use App\Mutator\CommandHandlerInterface;
use App\Repository\ArticleRepositoryInterface;
use App\Repository\CommentRepositoryInterface;
use App\Repository\MemberRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateCommentCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private MemberRepositoryInterface $memberRepository,
        private CommentRepositoryInterface $commentRepository,
        private ArticleRepositoryInterface $articleRepository,
    ) {
    }

    public function __invoke(CreateCommentCommand $command): Comment
    {
        $comment = (new Comment())
            ->setContent($command->content)
            ->setAuthor($this->memberRepository->searchWithId($command->authorId));

        if ($command->articleId !== null) {
            $comment->setArticle($this->articleRepository->searchWithId($command->articleId));
        } elseif ($command->parentId !== null) {
            $comment->setParent($this->commentRepository->searchWithId($command->parentId));
        }

        $this->commentRepository->add($comment, true);

        return $comment;
    }
}

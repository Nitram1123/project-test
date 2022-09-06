<?php

declare(strict_types=1);

namespace App\Mutator\Comment;

use App\Entity\Comment;
use App\Mutator\CommandHandlerInterface;
use App\Repository\CommentRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class UpdateCommentCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
    ) {
    }

    public function __invoke(UpdateCommentCommand $command): Comment
    {
        $comment = $this->commentRepository->searchWithId($command->id);
        \assert($comment !== null);

        if ($command->content !== null) {
            $comment->setContent($command->content);
        }

        if ($command->enabled !== null) {
            $comment->setEnabled($command->enabled);
        }

        $this->commentRepository->add($comment, true);

        return $comment;
    }
}

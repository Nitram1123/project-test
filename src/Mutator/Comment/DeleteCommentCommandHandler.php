<?php

declare(strict_types=1);

namespace App\Mutator\Comment;

use App\Mutator\CommandHandlerInterface;
use App\Repository\CommentRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class DeleteCommentCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
    ) {
    }

    public function __invoke(DeleteCommentCommand $command): void
    {
        $comment = $this->commentRepository->searchWithId($command->id);
        \assert($comment !== null);
        $this->commentRepository->remove($comment, true);
    }
}

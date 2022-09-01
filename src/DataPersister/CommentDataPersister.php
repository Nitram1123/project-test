<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Comment;
use App\Entity\Member;
use App\Repository\CommentRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class CommentDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private CommentRepository $commentRepository,
        private TokenStorageInterface $tokenStorage,
    ) {
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supports(mixed $data, array $context = []): bool
    {
        return $data instanceof Comment;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function persist(mixed $data, array $context = []): void
    {
        $member = $this->tokenStorage->getToken()->getUser();
        \assert($data instanceof Comment);
        \assert($member instanceof Member);

        $data->setAuthor($member);
        $this->commentRepository->add($data, true);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function remove(mixed $data, array $context = []): void
    {
        \assert($data instanceof Comment);
        $this->commentRepository->remove($data, true);
    }
}

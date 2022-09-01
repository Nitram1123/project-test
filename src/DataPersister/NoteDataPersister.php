<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Member;
use App\Entity\Note;
use App\Repository\NoteRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class NoteDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private NoteRepository $noteRepository,
        private TokenStorageInterface $tokenStorage,
    ) {
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supports(mixed $data, array $context = []): bool
    {
        return $data instanceof Note;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function persist(mixed $data, array $context = []): void
    {
        $member = $this->tokenStorage->getToken()->getUser();
        \assert($data instanceof Note);
        \assert($member instanceof Member);

        $data->setAuthor($member);
        $this->noteRepository->add($data, true);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function remove(mixed $data, array $context = []): void
    {
        \assert($data instanceof Note);
        $this->noteRepository->remove($data, true);
    }
}

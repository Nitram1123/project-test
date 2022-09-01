<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Article;
use App\Entity\Member;
use App\Repository\ArticleRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class ArticleDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private TokenStorageInterface $tokenStorage,
    ) {
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supports(mixed $data, array $context = []): bool
    {
        return $data instanceof Article;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function persist(mixed $data, array $context = []): void
    {
        $member = $this->tokenStorage->getToken()->getUser();
        \assert($data instanceof Article);
        \assert($member instanceof Member);

        $data->setAuthor($member);
        $this->articleRepository->add($data, true);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function remove(mixed $data, array $context = []): void
    {
        \assert($data instanceof Article);
        $this->articleRepository->remove($data, true);
    }
}

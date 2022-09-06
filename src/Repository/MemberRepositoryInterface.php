<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Member;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

interface MemberRepositoryInterface
{
    public function searchWithId(string $id): Member|null;

    public function add(Member $entity, bool $flush = false): void;

    public function remove(Member $entity, bool $flush = false): void;

    public function upgradePassword(PasswordAuthenticatedUserInterface $member, string $newHashedPassword): void;
}

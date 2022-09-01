<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Note;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class NoteVoter extends Voter
{
    public const NEW = 'NOTE_NEW';

    /** {@inheritdoc} */
    protected function supports(string $attribute, $subject): bool
    {
        return \in_array($attribute, [self::NEW], true)
            && $subject instanceof Note;
    }

    /** {@inheritdoc} */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        \assert($subject instanceof Note);
        $user = $token->getUser();
        if (! $user instanceof UserInterface) {
            return false;
        }

        return $subject->getComment()->getAuthor() !== $user;
    }
}

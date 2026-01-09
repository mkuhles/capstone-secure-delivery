<?php
declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Note;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;

final class NoteVoter extends Voter
{
    public const VIEW = 'NOTE_VIEW';
    public const EDIT = 'NOTE_EDIT';

    protected function supports(string $attribute, mixed $subject): bool {
        return $subject instanceof Note
            && \in_array($attribute, [self::VIEW, self::EDIT], true);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        /** @var Note $note */
        $note = $subject;

        // Symfony-typical admin override
        if (\in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        return $note->getOwner()?->getId() === $user->getId();
    }
}

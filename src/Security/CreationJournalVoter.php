<?php

namespace App\Security;

use App\Entity\CreationJournal;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CreationJournalVoter extends Voter
{
    public const EDIT = 'edit';
    public const DELETE = 'delete';
    public const VIEW = 'view';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE, self::VIEW])
            && $subject instanceof CreationJournal;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        /** @var CreationJournal $journal */
        $journal = $subject;

        return match($attribute) {
            self::EDIT => $this->canEdit($journal, $user),
            self::DELETE => $this->canDelete($journal, $user),
            self::VIEW => $this->canView($journal, $user),
            default => false,
        };
    }

    private function canEdit(CreationJournal $journal, User $user): bool
    {
        // Only the journal's artist owner can edit
        if ($journal->getArtist() && $journal->getArtist()->getUser() === $user) {
            return true;
        }

        // If journal has no artist but user has artist profile, can edit (shouldn't happen but for safety)
        if (!$journal->getArtist() && $user->getArtist()) {
            return true;
        }

        // Admins can edit any journal
        return in_array('ROLE_ADMIN', $user->getRoles());
    }

    private function canDelete(CreationJournal $journal, User $user): bool
    {
        // Only the journal's artist owner can delete
        if ($journal->getArtist() && $journal->getArtist()->getUser() === $user) {
            return true;
        }

        // If journal has no artist but user has artist profile, can delete (shouldn't happen but for safety)
        if (!$journal->getArtist() && $user->getArtist()) {
            return true;
        }

        // Admins can delete any journal
        return in_array('ROLE_ADMIN', $user->getRoles());
    }

    private function canView(CreationJournal $journal, User $user): bool
    {
        // Published journals are visible to everyone
        if ($journal->getIsPublished()) {
            return true;
        }

        // Unpublished journals are only visible to the owner
        if ($journal->getArtist() && $journal->getArtist()->getUser() === $user) {
            return true;
        }

        // Admins can view any journal
        return in_array('ROLE_ADMIN', $user->getRoles());
    }
}

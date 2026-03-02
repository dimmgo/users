<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserVoter extends Voter
{
    public const VIEW = 'USER_VIEW';
    public const EDIT = 'USER_EDIT';
    public const DELETE = 'USER_DELETE';
    public const CREATE = 'USER_CREATE';

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, $subject): bool
    {
        if ($attribute === self::CREATE) {
            return true;
        }

        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $currentUser = $token->getUser();

        if (!$currentUser instanceof User) {
            return false;
        }

        if ($this->security->isGranted(User::ROLE_ROOT)) {
            return true;
        }

        if ($this->security->isGranted(User::ROLE_USER)) {
            if ($attribute === self::DELETE) {
                return false;
            }

            if ($attribute === self::CREATE) {
                return false;
            }

            return $subject instanceof User && $currentUser->getId() === $subject->getId();
        }

        return false;
    }
}

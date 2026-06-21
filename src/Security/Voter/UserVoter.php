<?php

/**
 * User voter.
 */

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * User voter.
 *
 * Decides whether the current user may manage user accounts. Provides per-object
 * rules (e.g. an administrator cannot delete their own account), which protects
 * against IDOR-style access.
 *
 * @extends Voter<string, User|null>
 */
final class UserVoter extends Voter
{
    /**
     * Create permission.
     */
    public const CREATE = 'USER_CREATE';

    /**
     * Edit permission.
     */
    public const EDIT = 'USER_EDIT';

    /**
     * Delete permission.
     */
    public const DELETE = 'USER_DELETE';

    /**
     * Constructor.
     *
     * @param Security $security Security helper
     */
    public function __construct(private readonly Security $security)
    {
    }

    /**
     * Determines whether the voter supports the given attribute and subject.
     *
     * @param string $attribute Permission name
     * @param mixed  $subject   User entity, or null for the CREATE attribute
     *
     * @return bool Result
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (self::CREATE === $attribute) {
            return null === $subject;
        }

        return in_array($attribute, [self::EDIT, self::DELETE], true)
            && $subject instanceof User;
    }

    /**
     * Performs the access check.
     *
     * @param string         $attribute Permission name
     * @param mixed          $subject   Subject
     * @param TokenInterface $token     Security token
     * @param Vote|null      $vote      Vote explanation (Symfony 7.3+)
     *
     * @return bool Result
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $currentUser = $token->getUser();
        if (!$currentUser instanceof User) {
            return false;
        }

        $isAdmin = $this->security->isGranted('ROLE_ADMIN');

        return match ($attribute) {
            self::CREATE => $isAdmin,
            // Administrators manage any account; a user may edit their own.
            self::EDIT => $isAdmin || $this->isSameUser($subject, $currentUser),
            // Administrators may delete other accounts but never their own.
            self::DELETE => $isAdmin && !$this->isSameUser($subject, $currentUser),
            default => false,
        };
    }

    /**
     * Checks whether the subject is the currently logged-in user.
     *
     * @param mixed $subject     Subject
     * @param User  $currentUser Currently logged-in user
     *
     * @return bool Result
     */
    private function isSameUser(mixed $subject, User $currentUser): bool
    {
        return $subject instanceof User
            && null !== $subject->getId()
            && $subject->getId() === $currentUser->getId();
    }
}

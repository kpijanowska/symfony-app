<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Comment;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Comment voter.
 *
 * Decides whether the current user may delete a comment. Adding comments is
 * public, so it is not handled here.
 */
final class CommentVoter extends Voter
{
    /**
     * Delete permission.
     */
    public const DELETE = 'COMMENT_DELETE';

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
     * @param mixed  $subject   Comment entity
     *
     * @return bool Result
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return self::DELETE === $attribute && $subject instanceof Comment;
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
        if (!$token->getUser() instanceof UserInterface) {
            return false;
        }

        return $this->security->isGranted('ROLE_ADMIN');
    }
}

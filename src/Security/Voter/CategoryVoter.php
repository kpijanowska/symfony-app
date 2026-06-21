<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Category;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Category voter.
 *
 * Decides whether the current user may create, edit or delete a category.
 *
 * @extends Voter<string, Category|null>
 */
final class CategoryVoter extends Voter
{
    /**
     * Create permission.
     */
    public const CREATE = 'CATEGORY_CREATE';

    /**
     * Edit permission.
     */
    public const EDIT = 'CATEGORY_EDIT';

    /**
     * Delete permission.
     */
    public const DELETE = 'CATEGORY_DELETE';

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
     * @param mixed  $subject   Category entity, or null for the CREATE attribute
     *
     * @return bool Result
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (self::CREATE === $attribute) {
            return null === $subject;
        }

        return in_array($attribute, [self::EDIT, self::DELETE], true)
            && $subject instanceof Category;
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
    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token,
        ?Vote $vote = null,
    ): bool {
        if (!$token->getUser() instanceof UserInterface) {
            return false;
        }

        return $this->security->isGranted('ROLE_ADMIN');
    }
}

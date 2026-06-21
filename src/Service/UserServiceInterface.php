<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface UserServiceInterface.
 */
interface UserServiceInterface
{
    /**
     * Get paginated list of users.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<int, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Save entity.
     *
     * @param User        $user          User entity
     * @param string|null $plainPassword Optional new plain password to hash and set
     */
    public function save(User $user, ?string $plainPassword = null): void;

    /**
     * Delete entity.
     *
     * @param User $user User entity
     */
    public function delete(User $user): void;

    /**
     * Check whether the user can be deleted.
     *
     * @param User $user User entity
     *
     * @return bool Result
     */
    public function canBeDeleted(User $user): bool;

    /**
     * Change user password.
     *
     * @param User   $user        User entity
     * @param string $newPassword New plain password
     */
    public function changePassword(User $user, string $newPassword): void;
}

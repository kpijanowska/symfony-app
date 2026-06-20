<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;

/**
 * Interface UserServiceInterface.
 */
interface UserServiceInterface
{
    /**
     * Save entity.
     *
     * @param User $user User entity
     */
    public function save(User $user): void;

    /**
     * Change user password.
     *
     * @param User   $user        User entity
     * @param string $newPassword New plain password
     */
    public function changePassword(User $user, string $newPassword): void;
}

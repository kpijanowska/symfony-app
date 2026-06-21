<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; ++$i) {
            $user = new User();

            $user->setEmail(sprintf('user%d@example.com', $i));
            $user->setRoles([UserRole::ROLE_USER->value]);

            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    'user1234'
                )
            );

            $manager->persist($user);
        }

        for ($i = 0; $i < 3; ++$i) {
            $admin = new User();

            $admin->setEmail(sprintf('admin%d@example.com', $i));
            $admin->setRoles([
                UserRole::ROLE_USER->value,
                UserRole::ROLE_ADMIN->value,
            ]);

            $admin->setPassword(
                $this->passwordHasher->hashPassword(
                    $admin,
                    'admin1234'
                )
            );

            $manager->persist($admin);
        }

        $manager->flush();
    }
}

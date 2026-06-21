<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    protected Generator $faker;

    protected ObjectManager $manager;

    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();

        $articles = $manager
            ->getRepository(Article::class)
            ->findAll();

        for ($i = 0; $i < 10; ++$i) {
            $comment = new Comment();

            $comment->setNick($this->faker->userName());
            $comment->setEmail($this->faker->email());
            $comment->setContent($this->faker->paragraph());

            $comment->setCreatedAt(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );

            $comment->setArticle(
                $articles[array_rand($articles)]
            );

            $manager->persist($comment);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ArticleFixtures::class,
        ];
    }
}

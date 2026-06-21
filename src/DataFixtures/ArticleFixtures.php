<?php

/**
 * Article fixtures.
 */

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

/**
 * Class ArticleFixtures.
 */
class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    protected Generator $faker;

    /**
     * Load data.
     *
     * @param ObjectManager $manager Object manager
     */
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();

        $categories = $manager
            ->getRepository(Category::class)
            ->findAll();

        for ($i = 0; $i < 10; ++$i) {
            $article = new Article();

            $article->setTitle($this->faker->sentence());

            $article->setContent($this->faker->paragraph());

            $article->setCreatedAt(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );

            $article->setCategory(
                $categories[array_rand($categories)]
            );

            $manager->persist($article);
        }

        $manager->flush();
    }

    /**
     * Get dependencies.
     *
     * @return array<int, class-string> Dependencies
     */
    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}

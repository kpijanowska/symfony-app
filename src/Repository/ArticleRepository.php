<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public const PAGINATOR_ITEMS_PER_PAGE = 10;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder('article')
            ->select(
                'partial article.{id, title, content, createdAt}',
                'partial category.{id, name}'
            )
            ->join('article.category', 'category');
    }

    /**
     * Query articles by category.
     *
     * @param Category $category Category entity
     *
     * @return QueryBuilder Query builder
     */
    public function queryByCategory(Category $category): QueryBuilder
    {
        return $this->queryAll()
            ->andWhere('article.category = :category')
            ->setParameter('category', $category);
    }
    /**
     * Save entity.
     *
     * @param Article $article Article entity
     */

    public function save(Article $article): void
    {
        $this->getEntityManager()->persist($article);
        $this->getEntityManager()->flush();
    }
    /**
     * Delete entity.
     *
     * @param Article $article Article entity
     */
    public function delete(Article $article): void
    {
        $this->getEntityManager()->remove($article);
        $this->getEntityManager()->flush();
    }

    /**
     * Count articles by category.
     *
     * @param Category $category Category
     *
     * @return int Number of articles in category
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countByCategory(Category $category): int
    {
        return (int) $this->createQueryBuilder('article')
            ->select('COUNT(article.id)')
            ->where('article.category = :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->getSingleScalarResult();
    }

}

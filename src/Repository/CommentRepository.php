<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Comment>
 */
class CommentRepository extends ServiceEntityRepository
{
    public const PAGINATOR_ITEMS_PER_PAGE = 5;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder('comment');
    }

    /**
     * Find comments for a given article.
     *
     * @param Article $article Article entity
     *
     * @return Comment[] Array of comments
     */
    public function findByArticle(Article $article): array
    {
        return $this->createQueryBuilder('comment')
            ->andWhere('comment.article = :article')
            ->setParameter('article', $article)
            ->orderBy('comment.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Delete all comments belonging to a given article.
     *
     * @param Article $article Article entity
     */
    public function deleteByArticle(Article $article): void
    {
        $this->createQueryBuilder('comment')
            ->delete()
            ->andWhere('comment.article = :article')
            ->setParameter('article', $article)
            ->getQuery()
            ->execute();
    }
    /**
     * Save entity.
     *
     * @param Comment $comment Comment entity
     */

    public function save(Comment $comment): void
    {
        $this->getEntityManager()->persist($comment);
        $this->getEntityManager()->flush();
    }
    /**
     * Delete entity.
     *
     * @param Comment $comment Comment entity
     */
    public function delete(Comment $comment): void
    {
        $this->getEntityManager()->remove($comment);
        $this->getEntityManager()->flush();
    }
}

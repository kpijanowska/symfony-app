<?php

/**
 * Article service.
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\Article;
use App\Entity\Category;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class ArticleService.
 */
class ArticleService implements ArticleServiceInterface
{
    /**
     * Constructor.
     *
     * @param ArticleRepository  $articleRepository Article repository
     * @param CommentRepository  $commentRepository Comment repository
     * @param PaginatorInterface $paginator         Paginator
     */
    public function __construct(private readonly ArticleRepository $articleRepository, private readonly CommentRepository $commentRepository, private readonly PaginatorInterface $paginator)
    {
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<int, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->articleRepository->queryAll(),
            $page,
            ArticleRepository::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => [
                    'article.id',
                    'article.createdAt',
                    'article.title',
                    'article.content',
                    'category.name',
                ],
                'defaultSortFieldName' => 'article.createdAt',
                'defaultSortDirection' => 'desc',
            ]
        );
    }

    /**
     * Get paginated list of articles for a given category.
     *
     * @param int      $page     Page number
     * @param Category $category Category entity
     *
     * @return PaginationInterface<int, mixed> Paginated list
     */
    public function getPaginatedListByCategory(int $page, Category $category): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->articleRepository->queryByCategory($category),
            $page,
            ArticleRepository::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => [
                    'article.id',
                    'article.createdAt',
                    'article.title',
                    'article.content',
                ],
                'defaultSortFieldName' => 'article.createdAt',
                'defaultSortDirection' => 'desc',
            ]
        );
    }

    /**
     * Save entity.
     *
     * @param Article $article Article entity
     */
    public function save(Article $article): void
    {
        if (null === $article->getId()) {
            $article->setCreatedAt(
                new \DateTimeImmutable()
            );
        }
        $this->articleRepository->save($article);
    }

    /**
     * Delete entity.
     *
     * @param Article $article Article entity
     */
    public function delete(Article $article): void
    {
        // Relation is unidirectional, so remove dependent comments first
        // to avoid a foreign key constraint violation.
        $this->commentRepository->deleteByArticle($article);
        $this->articleRepository->delete($article);
    }
}

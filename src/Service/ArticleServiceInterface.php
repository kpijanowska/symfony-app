<?php

/**
 * Article service interface.
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\Article;
use App\Entity\Category;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface ArticleServiceInterface.
 */
interface ArticleServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<int, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Get paginated list of articles for a given category.
     *
     * @param int      $page     Page number
     * @param Category $category Category entity
     *
     * @return PaginationInterface<int, mixed> Paginated list
     */
    public function getPaginatedListByCategory(int $page, Category $category): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Article $article Article entity
     */
    public function save(Article $article): void;

    /**
     * Delete entity.
     *
     * @param Article $article Article entity
     */
    public function delete(Article $article): void;
}

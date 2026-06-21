<?php

/**
 * Comment service interface.
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\Article;
use App\Entity\Comment;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface CommentServiceInterface.
 */
interface CommentServiceInterface
{
    /**
     * Get paginated list of comments.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<int, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Get comments for a given article.
     *
     * @param Article $article Article entity
     *
     * @return Comment[] Array of comments
     */
    public function getCommentsByArticle(Article $article): array;

    /**
     * Save entity.
     *
     * @param Comment $comment Comment entity
     */
    public function save(Comment $comment): void;

    /**
     * Delete entity.
     *
     * @param Comment $comment Comment entity
     */
    public function delete(Comment $comment): void;
}

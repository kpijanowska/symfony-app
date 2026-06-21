<?php

/**
 * Comment service.
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\Article;
use App\Entity\Comment;
use App\Repository\CommentRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class CommentService.
 */
class CommentService implements CommentServiceInterface
{
    /**
     * Constructor.
     *
     * @param CommentRepository  $commentRepository Comment repository
     * @param PaginatorInterface $paginator         Paginator
     */
    public function __construct(private readonly CommentRepository $commentRepository, private readonly PaginatorInterface $paginator)
    {
    }

    /**
     * Get paginated list of comments.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<int, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->commentRepository->queryAll(),
            $page,
            CommentRepository::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => [
                    'comment.id',
                    'comment.nick',
                    'comment.email',
                    'comment.content',
                    'comment.createdAt',
                ],
                'defaultSortFieldName' => 'comment.createdAt',
                'defaultSortDirection' => 'desc',
            ]
        );
    }

    /**
     * Get comments for a given article.
     *
     * @param Article $article Article entity
     *
     * @return Comment[] Array of comments
     */
    public function getCommentsByArticle(Article $article): array
    {
        return $this->commentRepository->findByArticle($article);
    }

    /**
     * Save entity.
     *
     * @param Comment $comment Comment entity
     */
    public function save(Comment $comment): void
    {
        if (null === $comment->getId()) {
            $comment->setCreatedAt(
                new \DateTimeImmutable()
            );
        }
        $this->commentRepository->save($comment);
    }

    /**
     * Delete entity.
     *
     * @param Comment $comment Comment entity
     */
    public function delete(Comment $comment): void
    {
        $this->commentRepository->delete($comment);
    }
}

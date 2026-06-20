<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Article;
use App\Entity\Comment;
use App\Repository\CommentRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class CommentService implements CommentServiceInterface
{
    public function __construct(
        private readonly CommentRepository $commentRepository,
        private readonly PaginatorInterface $paginator,
    ) {
    }

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

    public function getCommentsByArticle(Article $article): array
    {
        return $this->commentRepository->findByArticle($article);
    }

    public function save(Comment $comment): void
    {
        if (null === $comment->getId()) {
            $comment->setCreatedAt(
                new \DateTimeImmutable()
            );
        }
        $this->commentRepository->save($comment);
    }

    public function delete(Comment $comment): void
    {
        $this->commentRepository->delete($comment);
    }
}

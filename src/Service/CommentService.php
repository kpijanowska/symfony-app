<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Comment;
use App\Repository\CommentRepository;

class CommentService implements CommentServiceInterface
{
    public function __construct(
        private readonly CommentRepository $commentRepository,
    ) {
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

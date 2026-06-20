<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Comment;

interface CommentServiceInterface
{
    public function save(Comment $comment): void;

    public function delete(Comment $comment): void;
}

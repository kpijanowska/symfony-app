<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class CommentController extends AbstractController
{
    #[Route('/comment', name: 'comment_index')]
    public function index(CommentRepository $commentRepository, PaginatorInterface $paginator,  #[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $paginator->paginate(
            $commentRepository->queryAll(),
            $page,
            CommentRepository::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['comment.id', 'comment.nick', 'comment.email', 'comment.content', 'comment.createdAt'],
                'defaultSortFieldName' => 'comment.createdAt',
                'defaultSortDirection' => 'desc',
            ]
        );

        return $this->render('comment/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }
    #[Route('/comment/{id}', name: 'comment_view')]
    public function view(Comment $comment): Response
    {
        return $this->render('comment/view.html.twig', [
            'comment' => $comment,
        ]);
    }
}

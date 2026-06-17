<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class TagController extends AbstractController
{
    #[Route('/tag', name: 'tag_index')]
    public function index(
        TagRepository $tagRepository,
        PaginatorInterface $paginator,
        #[MapQueryParameter] int $page = 1
    ): Response {
        $pagination = $paginator->paginate(
            $tagRepository->queryAll(),
            $page,
            TagRepository::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => [
                    'tag.id',
                    'tag.title',
                ],
                'defaultSortFieldName' => 'tag.title',
                'defaultSortDirection' => 'asc',
            ]
        );

        return $this->render('tag/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/tag/{id}', name: 'tag_view', methods: ['GET'])]
    public function view(Tag $tag): Response
    {
        return $this->render('tag/view.html.twig', [
            'tag' => $tag,
        ]);
    }
}

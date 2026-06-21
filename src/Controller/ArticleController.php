<?php

/**
 * Article controller.
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Article;
use App\Form\Type\ArticleType;
use App\Security\Voter\ArticleVoter;
use App\Service\ArticleServiceInterface;
use App\Service\CommentServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ArticleController.
 */
class ArticleController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param ArticleServiceInterface $articleService Article service
     * @param TranslatorInterface     $translator     Translator
     */
    public function __construct(private readonly ArticleServiceInterface $articleService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Index action.
     *
     * @param int $page Page number
     *
     * @return Response HTTP response
     */
    #[Route('/article', name: 'article_index')]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->articleService->getPaginatedList($page);

        return $this->render(
            'article/index.html.twig',
            [
                'pagination' => $pagination,
            ]
        );
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/article/create', name: 'article_create', methods: ['GET', 'POST'])]
    #[IsGranted(ArticleVoter::CREATE)]
    public function create(Request $request): Response
    {
        $article = new Article();

        $form = $this->createForm(
            ArticleType::class,
            $article
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->articleService->save($article);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('article_index');
        }

        return $this->render(
            'article/create.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Article $article Article entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/article/{id}/edit',
        name: 'article_edit',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET', 'PUT']
    )]
    #[IsGranted(ArticleVoter::EDIT, subject: 'article')]
    public function edit(Request $request, Article $article): Response
    {
        $form = $this->createForm(
            ArticleType::class,
            $article,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('article_edit', ['id' => $article->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->articleService->save($article);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('article_index');
        }

        return $this->render(
            'article/edit.html.twig',
            [
                'form' => $form->createView(),
                'article' => $article,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Article $article Article entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/article/{id}/delete',
        name: 'article_delete',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET', 'DELETE']
    )]
    #[IsGranted(ArticleVoter::DELETE, subject: 'article')]
    public function delete(Request $request, Article $article): Response
    {
        $form = $this->createForm(FormType::class, $article, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('article_delete', ['id' => $article->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->articleService->delete($article);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('article_index');
        }

        return $this->render(
            'article/delete.html.twig',
            [
                'form' => $form->createView(),
                'article' => $article,
            ]
        );
    }

    /**
     * View action.
     *
     * @param Article                 $article        Article entity
     * @param CommentServiceInterface $commentService Comment service
     *
     * @return Response HTTP response
     */
    #[Route('/article/{id}', name: 'article_view')]
    public function view(Article $article, CommentServiceInterface $commentService): Response
    {
        return $this->render('article/view.html.twig', [
            'article' => $article,
            'comments' => $commentService->getCommentsByArticle($article),
        ]);
    }
}

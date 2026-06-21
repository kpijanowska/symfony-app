<?php

/**
 * User controller.
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\ChangePasswordType;
use App\Form\Type\UserAdminType;
use App\Form\Type\UserType;
use App\Security\Voter\UserVoter;
use App\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserController.
 */
class UserController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param UserServiceInterface $userService User service
     * @param TranslatorInterface  $translator  Translator
     */
    public function __construct(private readonly UserServiceInterface $userService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Change password action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/admin/change-password', name: 'app_change_password', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function changePassword(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(
            ChangePasswordType::class,
            null,
            [
                'action' => $this->generateUrl('app_change_password'),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->changePassword(
                $user,
                $form->get('newPassword')->getData()
            );

            $this->addFlash(
                'success',
                $this->translator->trans('message.password_changed_successfully')
            );

            return $this->redirectToRoute('article_index');
        }

        return $this->render(
            'user/change_password.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Edit account action.
     *
     * The e-mail is the login identifier, so after changing it the current
     * session becomes stale. The admin is logged out and must sign in again
     * using the new e-mail.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/admin/edit', name: 'app_edit_account', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editAccount(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(
            UserType::class,
            $user,
            [
                'action' => $this->generateUrl('app_edit_account'),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->save($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.account_updated_relogin')
            );

            return $this->redirectToRoute('app_logout');
        }

        return $this->render(
            'user/edit_account.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Index action (user list).
     *
     * @param int $page Page number
     *
     * @return Response HTTP response
     */
    #[Route('/admin/user', name: 'user_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->userService->getPaginatedList($page);

        return $this->render('user/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/admin/user/create', name: 'user_create', methods: ['GET', 'POST'])]
    #[IsGranted(UserVoter::CREATE)]
    public function create(Request $request): Response
    {
        $user = new User();

        $form = $this->createForm(
            UserAdminType::class,
            $user,
            ['require_password' => true]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->save($user, $form->get('plainPassword')->getData());

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('user_index');
        }

        return $this->render(
            'user/create.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param User    $user    User entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/admin/user/{id}/edit',
        name: 'user_edit',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET', 'PUT']
    )]
    #[IsGranted(UserVoter::EDIT, subject: 'user')]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(
            UserAdminType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('user_edit', ['id' => $user->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->save($user, $form->get('plainPassword')->getData());

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('user_index');
        }

        return $this->render(
            'user/edit.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param User    $user    User entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/admin/user/{id}/delete',
        name: 'user_delete',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET', 'DELETE']
    )]
    #[IsGranted(UserVoter::DELETE, subject: 'user')]
    public function delete(Request $request, User $user): Response
    {
        if (!$this->userService->canBeDeleted($user)) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.cannot_delete_last_admin')
            );

            return $this->redirectToRoute('user_index');
        }

        $form = $this->createForm(FormType::class, $user, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('user_delete', ['id' => $user->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->delete($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('user_index');
        }

        return $this->render(
            'user/delete.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }
}

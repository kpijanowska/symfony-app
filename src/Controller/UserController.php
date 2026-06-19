<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\ChangePasswordType;
use App\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param UserServiceInterface $userService User service
     * @param TranslatorInterface  $translator  Translator
     */
    public function __construct(
        private readonly UserServiceInterface $userService,
        private readonly TranslatorInterface $translator
    ) {
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
}

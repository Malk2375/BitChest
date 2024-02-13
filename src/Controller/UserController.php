<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\UserType;
use App\Form\UserPasswordType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    /**
     * This controller allow us to edit user's profile
     * Both ROLE_USER and ROLE_ADMIN can edit user's profile because ROLE_ADMIN is a ROLE_USER
     *
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/edit_user/{id}', name: 'user.edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(
        User $choosenUser,
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $hasher
    ): Response {
        /**
         * Pour confirmer que c'est bien cet utilisateur qui voudrait modifier son profil.
         * Si l'utitilisateur n'est pas celui qui voudrait modifier son profil un message d'erreur sera affiché; erreur 403 : on peut la personnaliser depuis https://symfony.com/doc/current/controller/error_pages.html#controller-error-pages-by-status-code
         */

        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }
        if ($this->getUser() !== $choosenUser) {
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(UserType::class, $choosenUser);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($hasher->isPasswordValid($choosenUser, $form->getData()->getPlainPassword())) {
                $user = $form->getData();
                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Your profil has been updated.'
                );
                return $this->redirectToRoute('app_home');
            } else {
                $this->addFlash(
                    'warning',
                    'Incorrect password.'
                );
            };
        }

        return $this->render('pages/user/edit.html.twig', [
            'user' => $choosenUser,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/edit_password/{id}', name: 'user.edit.password', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function editPassword(
        User $user,
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $hasher
    ): Response {
        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }
        if ($this->getUser() !== $user) {
            return $this->redirectToRoute('app_home');
        }
        $form = $this->createForm(UserPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($hasher->isPasswordValid(
                $user,
                $form->getData()['plainPassword']
            )) {
                $user->setUpdatedAt(new \DateTimeImmutable());
                $user->setPlainPassword(
                    $form->getData()['newPassword']
                );
                $manager->persist($user);
                $manager->flush();
                $this->addFlash(
                    'success',
                    'Your password has been updated.'
                );
                return $this->redirectToRoute('app_home');
            } else {
                $this->addFlash(
                    'warning',
                    'Incorrect password.'
                );
            }
        }
        return $this->render('pages/user/edit_password.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }
}

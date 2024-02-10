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


class UserController extends AbstractController
{
    /**
     * This controller allows the user to edit his own profile.
     */
    #[Route('/edit_user/{id}', name: 'user.edit', methods: ['GET', 'POST'])]
    public function edit(User $user, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        /**
         * Pour confirmer que c'est bien cet utilisateur qui voudrait modifier son profil.
         * La deuxieme condition permet de le redirect vers Son profil; je n'ai pas encore une page profile mais des que je le fais je doit modifier cette route
         */
        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }
        if ($this->getUser() !== $user) {
            return $this->redirectToRoute('app_home');
        }


        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($hasher->isPasswordValid($user, $form->getData()->getPlainPassword())) {
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
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/edit_password/{id}', name: 'user.edit.password', methods: ['GET', 'POST'])]
    public function editPassword(Request $request, User $user,  EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
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

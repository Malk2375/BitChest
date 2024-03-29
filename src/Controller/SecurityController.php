<?php

namespace App\Controller;

use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Wallet;

class SecurityController extends AbstractController
{
    /**
     * Fonction permet de se connecter
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    #[Route('/login', name: 'security.login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('pages/security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUserName(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }
    /**
     *  This function allows you to logout.
     *
     * @return void
     */
    #[Route('/logout', name: 'security.logout')]
    public function logout()
    {
        // Pas de logique specifique ici car symfony s'occupe de ça
    }
    /**
     * Cette fonction permet que pour l'admin de créer un utilisateur
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/register', name: 'security.register', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function register(Request $request, EntityManagerInterface $manager): Response
    {
        $randomPassword = bin2hex(random_bytes(5)); // Génère une chaîne aleatoire pour l'utiliser comme un mot de passe
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        $user->setPlainPassword($randomPassword);
        // Créez une nouvelle instance de Wallet et définissez son solde sur 500
        $wallet = new Wallet();
        $wallet->setSolde(500);
        // Associez le portefeuille à l'utilisateur
        $wallet->setUser($user);
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $this->addFlash(
                'name',
                sprintf(
                    "Nom complet: %s",
                    $user->getFullName(),
                )
            );
            $this->addFlash(
                'email',
                sprintf(
                    "Email: %s",
                    $user->getEmail(),
                )
            );
            $this->addFlash(
                'password',
                sprintf(
                    "Random password: %s",
                    $user->getPlainPassword(),
                )
            );
            $manager->persist($user);
            $manager->persist($wallet);
            $manager->flush($user);
            return $this->redirectToRoute('app_home');
        }

        return $this->render('pages/security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

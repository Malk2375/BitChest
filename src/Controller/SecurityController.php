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

class SecurityController extends AbstractController
{
    /**
     * This controller allows you to login on your existing account
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
     * This controller allows you to logout.
     */
    #[Route('/logout', name: 'security.logout')]
    public function logout()
    {
        // Pas de logique specifique ici car symfony s'occupe de ça
    }
    /**
     * This controller allows you to register a new user.
     */
    #[Route('/register', name: 'security.register', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function register(Request $request, EntityManagerInterface $manager): Response
    {
        $randomPassword = bin2hex(random_bytes(5)); // Génère une chaîne hexadécimale de 20 caractères
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        $user->setPlainPassword($randomPassword);
        $user->setSolde(500.00);
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $this->addFlash(
                'success',
                sprintf(
                    "L'utilisateur a été bien créé . Nom complet: %s, Email: %s, Mot de passe Aléatoire: %s, Solde : %s.00 €",
                    $user->getFullName(),
                    $user->getEmail(),
                    $user->getPlainPassword(),
                    $user->getSolde()
                )
            );
            $manager->persist($user);
            $manager->flush($user);
            return $this->redirectToRoute('app_home');
        }

        return $this->render('pages/security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

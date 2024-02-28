<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\UserType;
use App\Form\UserPasswordType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\TransactionRepository;
use App\Repository\CryptoCurrencyRepository;


class UserController extends AbstractController
{
    /**
     * Ce contrôleur permet de modifier le profil de l'utilisateur.
     * Les utilisateurs avec le rôle ROLE_USER ou ROLE_ADMIN peuvent modifier leur profil. L'admin peut modifier n'importe quel profil, il a acccés
     *
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/edit_user/{id}', name: 'user.edit', methods: ['GET', 'POST'])]
    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_USER")'))]
    public function edit(
        User $user,
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $hasher
    ): Response {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }
        // Vérifier si l'utilisateur actuel est autorisé à modifier le profil
        if ($this->getUser() === $user) {
            // L'utilisateur actuel est l'utilisateur spécifié
        } elseif (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
            // L'utilisateur actuel a le rôle ROLE_ADMIN
        } else {
            // Redirection pour tous les autres cas
            return $this->redirectToRoute('app_home');
        }
        // Créer le formulaire de modification du profil
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($hasher->isPasswordValid($user, $form->getData()->getPlainPassword())) {
                $user = $form->getData();
                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'updateProfilSuccess',
                    'Profil updated successfully.'
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
    /**
     * Permet à un utilisateur de modifier son mot de passe.
     * Les utilisateurs avec le rôle ROLE_USER ou ROLE_ADMIN peuvent modifier leur mot de passe. Pareil l'admin peut modifier n'importe quel profil
     *
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    #[Route('/edit_password/{id}', name: 'user.edit.password', methods: ['GET', 'POST'])]
    #[IsGranted(new Expression('is_granted("ROLE_ADMIN")' or '(is_granted("ROLE_USER")'))]
    public function editPassword(
        User $user,
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $hasher
    ): Response {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }
        // Vérifier si l'utilisateur actuel est autorisé à modifier le mot de passe
        if ($this->getUser() === $user) {
            // L'utilisateur actuel est l'utilisateur spécifié
        } elseif (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
            // L'utilisateur actuel a le rôle ROLE_ADMIN
        } else {
            // Redirection pour tous les autres cas
            return $this->redirectToRoute('app_home');
        }

        // Créer le formulaire de modification du mot de passe
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
                    'updatePasswordSuccess',
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
    /**
     * Affiche le profil d'un utilisateur.
     * Les utilisateurs avec le rôle ROLE_USER ou ROLE_ADMIN peuvent afficher leur propre profil. L'admin peut voir tout
     *
     * @param User $user
     * @return Response
     */
    #[Route('/profil/{id}', name: 'user.profil', methods: ['GET', 'POST'])]
    #[IsGranted(new Expression('is_granted("ROLE_ADMIN")' or '(is_granted("ROLE_USER")'))]
    public function profil(
        User $user,
    ) {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }
        // Vérifier si l'utilisateur actuel est autorisé à afficher le profil
        if ($this->getUser() === $user) {
            // L'utilisateur actuel est l'utilisateur spécifié
        } elseif (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
            // L'utilisateur actuel a le rôle ROLE_ADMIN
        } else {
            // Redirection pour tous les autres cas
            return $this->redirectToRoute('app_home');
        }
        return $this->render('pages/user/profil.html.twig', [
            'user' => $user,
        ]);
    }
    /**
     * Affiche toutes les transactions d'un utilisateur.
     * Les utilisateurs avec le rôle ROLE_USER ou ROLE_ADMIN peuvent afficher les transactions de leur propre profil ou celui des autres utilisateurs. L'admin peut voir les transactions de tous les utilisateurs
     * @param User $user
     * @param TransactionRepository $transactionRepository
     * @param CryptoCurrencyRepository $cryptoCurrencyRepository
     * @return Response
     */
    #[Route('/user_transactions/{id}', name: 'user.transactions', methods: ['GET', 'POST'])]
    #[IsGranted(new Expression('is_granted("ROLE_ADMIN")' or '(is_granted("ROLE_USER")'))]
    public function transactions(
        User $user,
        TransactionRepository $transactionRepository,
        CryptoCurrencyRepository $cryptoCurrencyRepository
    ) {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }
        // Vérifier si l'utilisateur actuel est autorisé à afficher les transactions
        if ($this->getUser() === $user || in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
            $transactions = $transactionRepository->findBy(['user' => $user]);
            $transactionDetails = [];
            foreach ($transactions as $transaction) {
                // Récupérer l'objet CryptoCurrency associé à l'ID de la transaction
                $cryptoCurrency = $cryptoCurrencyRepository->find($transaction->getCrypto());
                // Recuperer le nom de la crypto-monnaie
                $cryptoName = $cryptoCurrency ? $cryptoCurrency->getName() : null;
                // Créer un tableau associatif contenant les détails de la transaction, y compris le nom de la crypto-monnaie
                $transactionDetails[] = [
                    'type' => $transaction->getType(),
                    'amount' => $transaction->getAmount(),
                    'price' => $transaction->getPrice(),
                    'cryptoName' => $cryptoName,
                    'transactionDate' => $transaction->getTransactionDate(),
                    'transactionPrice' => $transaction->getAmount() * $transaction->getPrice(),
                ];
            }
            return $this->render('pages/user/user_transactions.html.twig', [
                'user' => $user,
                'transactions' => $transactionDetails,
            ]);
        } else {
            return $this->redirectToRoute('app_home');
        }
    }
    /**
     * Affiche la liste des utilisateurs pour l'admin. Les utilisateurs normaux n'auront pas cette liste
     *
     * @param UserRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/usersdisplay', name: 'users.display', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function usersDisplay(
        UserRepository $repository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $users = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            10
        );
        // dd($users);
        return $this->render('pages/admin/usersDisplay.html.twig', [
            'users' => $users,
        ]);
    }
    /**
     * Supprime un utilisateur.
     *
     * @param User $user
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/delete_user/{id}', name: 'user.delete', methods: ['GET', 'POST'])]
    public function delete_user(
        User $user,
        EntityManagerInterface $manager
    ) {
        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }
        if ($this->getUser() === $user) {
            // L'utilisateur actuel est l'utilisateur spécifié
        } elseif (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
            // L'utilisateur actuel a le rôle ROLE_ADMIN
        } else {
            // Redirection pour tous les autres cas
            return $this->redirectToRoute('app_home');
        }

        $manager->remove($user);
        $manager->flush();
        $this->addFlash(
            'deleteUserSuccess',
            'Profil deleted successfully.'
        );
        return $this->redirectToRoute('users.display');
    }
}

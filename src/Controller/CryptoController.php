<?php

namespace App\Controller;

use App\Entity\CryptoCurrency;
use App\Form\BuyCryptoType;
use App\Repository\CryptoCurrencyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Transaction;
use App\Entity\User;
use App\Entity\Wallet;

class CryptoController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/crypto', name: 'crypto_list')]
    #[IsGranted('ROLE_USER')]
    public function list(CryptoCurrencyRepository $cryptoCurrencyRepository): Response
    {
        $cryptos = $cryptoCurrencyRepository->findBy([], ['name' => 'ASC']);

        return $this->render('crypto/list.html.twig', [
            'cryptos' => $cryptos,
        ]);
    }

    #[Route('/crypto/buycrypto', name: 'crypto_buy')]
    #[IsGranted('ROLE_USER')]
    public function buyCrypto(
        User $user,
        Request $request,
        Wallet $wallet,
        EntityManagerInterface $manager,
    ): Response {
        // if (!$this->getUser()) {
        //     return $this->redirectToRoute('security.login');
        // }
        // if ($this->getUser() === $user) {
        //     // L'utilisateur actuel est l'utilisateur spécifié
        // } elseif (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
        //     // L'utilisateur actuel a le rôle ROLE_ADMIN
        // } else {
        //     // Redirection pour tous les autres cas
        //     return $this->redirectToRoute('app_home');
        // }
        $transaction = new Transaction(); // Crée une nouvelle instance de Transaction
        $user = $this->getUser();
        $solde = $user->wallet?->getSolde();
        $form = $this->createForm(BuyCryptoType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $transaction = $form->getData();
            $transaction->setUser($user);
            $amount = $transaction->getAmount();
            $crypto = $transaction->getCrypto();

            // Récupérer la crypto-monnaie sélectionnée à partir de la base de données
            $crypto = $this->entityManager->getRepository(CryptoCurrency::class)->find($crypto);

            if (!$crypto) {
                throw $this->createNotFoundException('Crypto-monnaie non trouvée.');
            }
            // Utiliser le prix actuel de la crypto-monnaie comme prix de la transaction
            $price = $crypto->getCurrentPrice();
            $transaction->setPrice($price);
            $transaction->setCrypto($crypto);
            $newSolde = $solde - ($amount * $price);
            $user->wallet?->setSolde($newSolde);
            $this->addFlash(
                'buysuccess',
                sprintf(
                    "Amount of Crypto bought :  %s",
                    $transaction->getAmount(),
                )
            );

            $manager->persist($transaction);
            $manager->flush();

            // Rediriger vers l'action d'achat avec les données sélectionnées
            return $this->redirectToRoute('app_home');
        }

        return $this->render('crypto/buy.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

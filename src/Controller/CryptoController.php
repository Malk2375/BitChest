<?php

namespace App\Controller;

use App\Entity\CryptoCurrency;
use App\Form\BuyCryptoType;
use App\Form\SellCryptoType;
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
        $transaction = new Transaction(); // Crée une nouvelle instance de Transaction
        $user = $this->getUser();
        $solde = $user->wallet?->getSolde();
        $form = $this->createForm(BuyCryptoType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $transaction = $form->getData();
            $transaction->setUser($user);
            $transaction->setType("BUY");
            $amount = $transaction->getAmount();
            $crypto = $transaction->getCrypto();

            // Récupérer la crypto-monnaie sélectionnée à partir de la base de données
            $crypto = $this->entityManager->getRepository(CryptoCurrency::class)->find($crypto);
            // Utiliser le prix actuel de la crypto-monnaie comme prix de la transaction
            $price = $crypto->getCurrentPrice();
            $transaction->setPrice($price);
            $transaction->setCrypto($crypto);
            if ($amount * $price <= $solde) {
                $newSolde = $solde - ($amount * $price);
                $user->wallet?->setSolde($newSolde);
                $userCryptoAmounts = $user->wallet?->getUserCryptoAmounts();
                $cryptoName = $crypto->getName();
                $userCryptoAmounts[$cryptoName] = ($userCryptoAmounts[$cryptoName] ?? 0) + $amount;
                $user->wallet?->setUserCryptoAmounts($userCryptoAmounts);
                $this->addFlash(
                    'buysuccess',
                    sprintf(
                        "You have bought : %s %s , with : %s €, Your new Solde : %s €",
                        $transaction->getAmount(),
                        $cryptoName,
                        $amount * $price,
                        $newSolde,
                    )
                );

                $manager->persist($transaction);
                $manager->flush();

                // Rediriger vers l'action d'achat avec les données sélectionnées
                return $this->redirectToRoute('app_home');
            } else {
                $this->addFlash(
                    'buyerror',
                    sprintf(
                        "You don't have enough money to buy : %s %s, with : %s €, Your Solde is : %s €",
                        $transaction->getAmount(),
                        $crypto->getName(),
                        $amount * $price,
                        $solde,
                    )
                );
                return $this->redirectToRoute('app_home');
            }
        }

        return $this->render('crypto/buy.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/crypto/sellcrypto', name: 'crypto_sell')]
    #[IsGranted('ROLE_USER')]
    public function sellCrypto(
        User $user,
        Request $request,
        Wallet $wallet,
        EntityManagerInterface $manager,
    ): Response {
        $transaction = new Transaction(); // Crée une nouvelle instance de Transaction
        $user = $this->getUser();
        $solde = $user->wallet?->getSolde();
        $form = $this->createForm(SellCryptoType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $transaction = $form->getData();
            $transaction->setUser($user);
            $transaction->setType("SELL");
            $amount = $transaction->getAmount();
            $crypto = $transaction->getCrypto();

            // Récupérer la crypto-monnaie sélectionnée à partir de la base de données
            $crypto = $this->entityManager->getRepository(CryptoCurrency::class)->find($crypto);
            // Utiliser le prix actuel de la crypto-monnaie comme prix de la transaction
            $price = $crypto->getCurrentPrice();
            $transaction->setPrice($price);
            $transaction->setCrypto($crypto);
            $userCryptoAmounts = $user->wallet?->getUserCryptoAmounts();
            $cryptoName = $crypto->getName();
            if (isset($userCryptoAmounts[$cryptoName]) && $userCryptoAmounts[$cryptoName] >= $amount) {
                $newSolde = $solde + ($amount * $price);
                $user->wallet?->setSolde($newSolde);
                $userCryptoAmounts[$cryptoName] -= $amount; // Soustraire la quantité vendue du portefeuille
                $user->wallet?->setUserCryptoAmounts($userCryptoAmounts);
                $this->addFlash(
                    'sellsuccess',
                    sprintf(
                        "You have sold : %s %s , with : %s €, Your new Solde : %s €",
                        $amount,
                        $cryptoName,
                        $amount * $price,
                        $newSolde,
                    )
                );
                $manager->persist($transaction);
                $manager->flush();
                // Rediriger vers l'action d'achat avec les données sélectionnées
                return $this->redirectToRoute('app_home');
            } else {
                $this->addFlash(
                    'sellerror',
                    sprintf(
                        "You don't have enough %s to sell %s.",
                        $cryptoName,
                        $amount,
                    )
                );
                return $this->redirectToRoute('app_home');
            }
        }
        return $this->render('crypto/sell.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

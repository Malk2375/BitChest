<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;
use App\Entity\Transaction; // Importez l'entité Transaction
use Doctrine\ORM\EntityManagerInterface; // Importez l'EntityManagerInterface
use App\Repository\TransactionRepository; // Importez TransactionRepository

class WalletController extends AbstractController
{
    /**
     * Affiche la portefeuille de l'utilisateur, solde, quantité de crypto qu'il a
     *
     * @param EntityManagerInterface $entityManager
     * @return void
     */
    #[Route('/wallet', name: 'app_wallet')]
    #[IsGranted('ROLE_USER')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $userCryptoAmounts = $user->wallet?->getUserCryptoAmounts();
        $transactionRepository = $entityManager->getRepository(Transaction::class);
        // // On va stocker les transactions d'un utilisateur dans un tableau associatif et l'afficher dans la vue twig
        $userTransactions = $transactionRepository->findBy(['user' => $user]);

        // $userCryptoAmounts = [];
        foreach ($userTransactions as $transaction) {
            //     $cryptoName = $transaction->getCrypto()->getName();
            //     $amount = $transaction->getAmount();
            $price = $transaction->getPrice();
            $current_price = $transaction->getCrypto()->getCurrentPrice();

            //     $userCryptoAmounts[$cryptoName] = [
            //         'amount' => $amount,
            //         'price' => $price,
            //         'current_price' => $current_price,
            //     ];
        }
        return $this->render('pages/wallet/index.html.twig', [
            'userCryptoAmounts' => $userCryptoAmounts,
            'current_price' => $current_price,
            'price' => $price,
        ]);
    }
}

<?php

namespace App\Controller;

use App\Repository\CryptoCurrencyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CryptoController extends AbstractController
{
    #[Route('/crypto', name: 'crypto_list')]
    #[IsGranted('ROLE_USER')]
    public function list(CryptoCurrencyRepository $cryptoCurrencyRepository): Response
    {
        $cryptos = $cryptoCurrencyRepository->findAll();

        return $this->render('crypto/list.html.twig', [
            'cryptos' => $cryptos,
        ]);
    }
}

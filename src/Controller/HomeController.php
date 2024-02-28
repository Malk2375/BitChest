<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * Afficher la page d'acceuil
     *
     * @return Response
     */
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('pages/home/home.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}

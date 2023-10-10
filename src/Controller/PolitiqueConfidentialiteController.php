<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PolitiqueConfidentialiteController extends AbstractController
{
    #[Route('/politique/confidentialite', name: 'app_politique_confidentialite')]
    public function index(): Response
    {
        return $this->render('politique_confidentialite/index.html.twig', [
            'controller_name' => 'PolitiqueConfidentialiteController',
        ]);
    }
}

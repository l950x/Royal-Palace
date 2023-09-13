<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConfirmationController extends AbstractController
{
    #[Route('/confirmation', name: 'app_confirmation')]
    public function index(): Response
    {
        return $this->render('Confirmation/index.html.twig', [
            'controller_name' => 'ConfirmationController',
        ]);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        
        $referer = $request->headers->get('referer');

        // Sauvegardez-la dans la session
        $session = $request->getSession();
        $session->set('_security.main.target_path', $referer);

        // Vérifiez si l'utilisateur est connecté avec succès
        if ($this->isGranted('ROLE_USER')) {
            // Récupérez la route précédente depuis la session
            $session = $request->getSession();
            $targetPath = $session->get('_security.main.target_path');

            if ($targetPath) {
                // Redirigez l'utilisateur vers la route précédente
                return $this->redirect($targetPath);
            } else {
                // Redirigez l'utilisateur vers une route par défaut
                return $this->redirectToRoute('app_profile');
            }
        
    }
    $error = $authenticationUtils->getLastAuthenticationError();
    $lastUsername = $authenticationUtils->getLastUsername();
    return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);

}

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}

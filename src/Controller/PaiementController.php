<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\PaiementType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ChambreRepository;
use App\Entity\Reserver;

class PaiementController extends AbstractController
{
    #[Route('/paiement', name: 'app_paiement')]
    public function index(Request $request, SessionInterface $session, EntityManagerInterface $entityManager, ChambreRepository $chambreRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

            $dateEntree = $session->get('dateEntree');
            $dateSortie = $session->get('dateSortie');
            $dateEntreeFormat = $dateEntree->format('Y-m-d');
            $dateSortieFormat = $dateSortie->format('Y-m-d');
            //TODO: Dates avant liste chambres, AncienneReservaion dans db

        $chambreId = $session->get('chambreId');
        $price = $session->get('price');
        $nbPersonne = $session->get('nbPersonne');
        $prixTotal = $price * $dateSortie->diff($dateEntree)->days * $nbPersonne;
        
        $formPaiement = $this->createForm(PaiementType::class);
        
        $formPaiement->handleRequest($request);
        
        if ($formPaiement->isSubmitted() && $formPaiement->isValid()) {
            
            $data = $formPaiement->getData();
            // $dateEntree = $data['dateEntree'];
            // $dateSortie = $data['dateSortie'];
            // $dateEntree = \DateTimeImmutable::createFromMutable($dateEntree);
            // $dateSortie = \DateTimeImmutable::createFromMutable($dateSortie);
            $dateEntreeFormat = $dateEntree->format('Y-m-d');
            $dateSortieFormat = $dateSortie->format('Y-m-d');
            $chambre = $chambreRepository->find($chambreId);

            if (!$chambre) {
                throw $this->createNotFoundException('Chambre non trouvée');
            }

            $reservation = new Reserver();
            $reservation->setUser($user);
            $reservation->setChambre($chambre);
            $reservation->setDateEntree($dateEntree);
            $reservation->setDateSortie($dateSortie);
            $reservation->setPrix($prixTotal);
            $reservation->setValidite(0);

            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->render('Confirmation/index.html.twig', [
                'user' => $user,
                'dateEntree' => $dateEntreeFormat,
                'dateSortie' => $dateSortieFormat,
                'price' => $prixTotal,
                'chambre' => $chambreId,
            ]);
        }

        return $this->render('paiement/index.html.twig', [
            'controller_name' => 'PaiementController',
            'form' => $formPaiement->createView(),
            'dateEntree' => $dateEntreeFormat,
            'dateSortie' => $dateSortieFormat,
            'price' => $prixTotal,
        ]);
    }
}

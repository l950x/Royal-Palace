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
use App\Repository\ReserverRepository;

class PaiementController extends AbstractController
{
    #[Route('/paiement/{edit}', name: 'app_paiement', requirements: ['edit' => '\d+'])]
    public function index(Request $request, SessionInterface $session, EntityManagerInterface $entityManager, ChambreRepository $chambreRepository, ReserverRepository $reserverRepository, $edit = 0): Response
    {

        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $preChambre = $session->get('preChambre');
        $dateEntree = $session->get('dateEntree');
        $dateSortie = $session->get('dateSortie');
        $dateEntreeFormat = $dateEntree->format('Y-m-d');
        $dateSortieFormat = $dateSortie->format('Y-m-d');

        if ($preChambre) {
            $chambreId = $preChambre;
        } else {
            $chambreId = $session->get('chambreId');
        }
        
        $price = $session->get('price');
        $nbPersonne = $session->get('nbPersonne');
        $prixTotal = $price * $dateSortie->diff( $dateEntree)->days * $nbPersonne;
        $formPaiement = $this->createForm(PaiementType::class);
        $formPaiement->handleRequest($request);

        if ($formPaiement->isSubmitted() && $formPaiement->isValid()) {

            $data = $formPaiement->getData();
            $dateEntreeFormat = $dateEntree->format('Y-m-d');
            $dateSortieFormat = $dateSortie->format('Y-m-d');
            $chambre = $chambreRepository->find($chambreId);

            if (!$chambre) {
                throw $this->createNotFoundException('Chambre non trouvée');
            }

            if (!$edit) { // si edit = 1 c'est qu'on viens de la page de modif de reservation, sinon c'est qu'on veux reserver 
                
                // $reservation = $reserverRepository->findOneBy([
                //     'chambre' => $chambreId,
                // ]);

                // if (!$reservation) {
                    $reservation = new Reserver();
                    $reservation->setUser($user)
                        ->setChambre($chambre)
                        ->setDateEntree($dateEntree)
                        ->setDateSortie($dateSortie)
                        ->setPrix($prixTotal)
                        ->setNbPersonne($nbPersonne)
                        ->setValidite(0);
                    $entityManager->persist($reservation);
                    $entityManager->flush();
                    $reservationId = $reservation->getId();
                    $session->set('reservationId', $reservation->getId());

                // } else {
                    // throw $this->createNotFoundException("Y'a déjà une reservation pour cette chambre");
                    
                    return $this->render('Confirmation/index.html.twig', [
                        'user' => $user,
                        'dateEntree' => $dateEntreeFormat,
                        'dateSortie' => $dateSortieFormat,
                        'price' => $prixTotal,
                        'chambre' => $chambreId,
                        'edit' => $edit,
                        'reservationId' => $reservationId,
                    ]);
                }
                else { // si edit = 1 :
                $reservation = $reserverRepository->findOneBy([
                    'chambre' => $chambreId,
                ]);

                if (!$reservation) {
                    throw $this->createNotFoundException('Reservation non trouvé');
                } else {
                    $reservation->setDateEntree($dateEntree)
                        ->setDateSortie($dateSortie)
                        ->setPrix($prixTotal);
                    $entityManager->persist($reservation);
                    $entityManager->flush();

                    return $this->render('Confirmation/index.html.twig', [
                        'user' => $user,
                        'dateEntree' => $dateEntreeFormat,
                        'dateSortie' => $dateSortieFormat,
                        'price' => $prixTotal,
                        'chambre' => $chambreId,
                        'edit' => $edit,
                    ]);
                }
            }
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

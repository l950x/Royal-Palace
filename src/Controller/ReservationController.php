<?php

namespace App\Controller;

use App\Entity\Chambre;
use App\Repository\ChambreRepository;
use App\Form\ReservationType;
use App\Repository\ReserverRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\DBAL\Connection;

class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'app_reservation')]
    public function index(Request $request, ChambreRepository $chambreRepository, Connection $connection, ReserverRepository $reserverRepository, SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $today = new \DateTimeImmutable();
        $today = $today->format('Y-m-d');
        $dql = "SELECT * FROM reserver WHERE date_sortie <= '$today' ";
        // $dql = "SELECT * FROM reserver WHERE date_sortie <= :today";
        $ansReservation = $connection->executeQuery($dql)->fetchAll();

        foreach ($ansReservation as $reservationData) {
            $reservations = $reserverRepository->findBy([
                'id' => $reservationData['id'],
            ]);

            if ($reservations) {
                foreach ($reservations as $reservation) {
                    $reservation->setValidite(1);
                    $entityManager->persist($reservation);
                }
            }
        }
        $entityManager->flush();

        $session->set('price', null);
        $session->set('dateEntree', null);
        $session->set('dateSortie', null);
        $session->set('nbPersonne', null);
        $session->set('chambreId', null);
        $session->set('preChambre', null);

        $formReservation = $this->createForm(ReservationType::class);
        $formReservation->handleRequest($request);

        if ($formReservation->isSubmitted() && $formReservation->isValid()) {
            $data = $formReservation->getData();
            $options = [];

            $dateEntree = $data['arrivee'];
            $dateSortie = $data['depart'];
            $nbPersonne = $data['nbPersonne'];

            $formData = array(
                "VueSurMer" => $data['option'],
                "ChaineALaCarte" => $data['option2'],
                "Climatisation" => $data['option3'],
                "TelevisionEcranPlat" => $data['option4'],
                "Telephone" => $data['option5'],
                "ChaineSatellite" => $data['option6'],
                "ChaineDuCable" => $data['option7'],
                "CoffreFort" => $data['option8'],
                "WifiGratuit" => $data['option9'],
                "MaterielDeRepassage" => $data['option10']
            );

            foreach ($formData as $key => $value) {
                if ($value) {
                    $options[$key] = $value;
                }
            }

            $chambres = $chambreRepository->findBy($options);

            if ($chambres) {
                
                $i = 0;
                do {
                    $chamb = $chambres[array_rand($chambres)];
                    $id = $chamb->getId();
                    $reserver = $reserverRepository->findOneBy([
                        'chambre' => $id,
                        'validite' => 0
                    ]);
                    $i++;
                } while ($reserver && $i < 5);
                
                if ($reserver) { //double check pour être sur de tomber sur une chambre disponible
                    $session->set('dateEntree', $dateEntree);
                    $session->set('dateSortie', $dateSortie);
                    $session->set('nbPersonne', $nbPersonne);
                    return $this->render('reservation/index.html.twig', [
                        'controller_name' => 'ReservationController',
                        'form' => $formReservation,
                        'error' => 'Aucune chambre disponible pour ces options',
                    ]);
                }

                $session->set('dateEntree', $dateEntree);
                $session->set('dateSortie', $dateSortie);
                $session->set('nbPersonne', $nbPersonne);
                $session->set('price', $chamb->getTarif());
                $session->set('chambreId', $id);

            } else {

                $session->set('dateEntree', $dateEntree);
                $session->set('dateSortie', $dateSortie);
                $session->set('nbPersonne', $nbPersonne);
                return $this->render('reservation/index.html.twig', [
                    'controller_name' => 'ReservationController',
                    'form' => $formReservation,
                    'error' => 'Aucune chambre disponible pour ces options',
                ]);
                
            }

                return $this->redirectToRoute('app_chambre_show', [
                    'id' => $id,
                ]);

        }

        return $this->render('reservation/index.html.twig', [
            'controller_name' => 'ReservationController',
            'form' => $formReservation,
            'error' => null,
        ]);
    }
}

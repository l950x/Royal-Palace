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
        $session->set('price', null);
        $session->set('dateEntree', null);
        $session->set('dateSortie', null);
        $session->set('nbPersonne', null);
        $session->set('chambreId', null);
        //set les sessions a 0 pour eviter les problemes

        $formReservation = $this->createForm(ReservationType::class);
        $formReservation->handleRequest($request);

        if ($formReservation->isSubmitted() && $formReservation->isValid()) {
            $data = $formReservation->getData();
            $options = [];

            $dateEntree = $data['dateEntree'];
            $dateSortie = $data['dateSortie'];
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
            //tableau avec toute les option 0 et 1

            foreach ($formData as $key => $value) {
                if ($value) {
                    $options[$key] = $value;
                }
            }

            //on recupere que les 1 dans $options (que ceux que le user a choisis)

            // $randomChambre = $entityManager->getRepository(Chambre::class)->findAvailableChambres($formData, $dateEntree, $dateSortie);
            $chambres = $chambreRepository->findBy($options);



            $today = new \DateTimeImmutable();
            $today = $today->format('Y-m-d');
            $dql = "SELECT * FROM reserver WHERE date_sortie <= '$today' "; // on recup les reservations terminé
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


            //on recupere les chambres avec au moins les options que le user a choisis

            if ($chambres) {
                do {
                    $chamb = $chambres[array_rand($chambres)];
                    $id = $chamb->getId();

                    $reserver = $reserverRepository->findOneBy([
                        'chambre' => $id,
                        'validite' => 0
                    ]);
                } while ($reserver);

                //prend une chambre au pif ($chamb) et cherche si elle est déjà reserver, tant que $reserver n'est pas null ça cherche une autre chambre

                $session->set('price', $chamb->getTarif());
                $session->set('dateEntree', $dateEntree);
                $session->set('dateSortie', $dateSortie);
                $session->set('nbPersonne', $nbPersonne);
                $session->set('chambreId', $id);

                //set les infos de la chambre et du form en sessions 

                return $this->redirectToRoute('app_chambre_show', [
                    'id' => $id,

                ]);

                //on affiche la chambre que le programme a choisis (it's lit)


            } else {
                throw $this->createNotFoundException('Aucune chambre disponible (Check la db)');
            }
        }

        return $this->render('reservation/index.html.twig', [
            'controller_name' => 'ReservationController',
            'form' => $formReservation,
        ]);
    }
}

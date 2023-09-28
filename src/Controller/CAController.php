<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\DatesType;
use App\Repository\ChambreRepository;
use App\Repository\ReserverRepository;
use DateTimeImmutable;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Doctrine\DBAL\Connection;

class CAController extends AbstractController
{
    #[Route('/ca', name: 'app_ca')]
    public function index(Request $request, ChartBuilderInterface $chartBuilder, Connection $connection, ChambreRepository $chambreRepository, ReserverRepository $reserverRepository): Response
    {

        $user = $this->getUser();
        if (!$user || !$this->isGranted('ROLE_EMPLOYE')) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(DatesType::class, [
            'dateEntree' => new DateTimeImmutable('last year'),
            'dateSortie' => new DateTimeImmutable('next year'),
        ]);
        $form->handleRequest($request);

        // $dateDebut = '2023-09-17';
        // $dateFin = '2023-09-18';
        // $chiffreAffaires = 0;


        if ($form->isSubmitted()) {
            $data = $form->getData();
            $dateDebut = $data['dateEntree'];
            $dateFin = $data['dateSortie'];
            // $chiffreAffaires = $this->calculerCA($dateDebut, $dateFin, $connection);

        } else {
            $dateDebut = new DateTimeImmutable('last year');
            $dateFin = new DateTimeImmutable('next year');
        }

        $idChambres = $this->idChambres($dateDebut, $dateFin, $connection);
        $chambres = $chambreRepository->findBy(['id' => $idChambres]);

        $CAT1 = $CAT2 = $CAT3 = 0;
        $total = 0;


        foreach ($chambres as $chambre) {
            $id = $chambre->getId();
            $reserver = $reserverRepository->findBy(['chambre' => $id]); //cherche la reservation pour la chambre 
            $prixTotal = 0;

            foreach ($reserver as $reservation) {
                $prixTotal += $reservation->getPrix(); //recupere le prix de la reservation
            }

            switch ($chambre->getType()) { // si la categorie de la chambre = 1, alors ça met son prix dans le CAT1, etc
                case 1:
                    $CAT1 += $prixTotal;
                    break;
                case 2:
                    $CAT2 += $prixTotal;
                    break;
                case 3:
                    $CAT3 += $prixTotal;
                    break;
            }
            $total = $CAT1 + $CAT2 + $CAT3;
        }

        return $this->render('ca/index.html.twig', [
            'controller_name' => 'CAController',
            'form' => $form,
            'CAT1' => $CAT1,
            'CAT2' => $CAT2,
            'CAT3' => $CAT3,
            'total' => $total
        ]);
    }

    // public function calculerCA($dateDebut, $dateFin, Connection $connection)
    // {
    //     $dateDebut = $dateDebut->format('Y-m-d');
    //     $dateFin = $dateFin->format('Y-m-d');
    //     $sql = "
    //     SELECT SUM(prix) as chiffre_affaires
    //     FROM reserver
    //     WHERE date_entree BETWEEN '$dateDebut' AND '$dateFin'
    // ";
    //     $result = $connection->executeQuery($sql)->fetchOne();
    //     return $result;
    // }

    public function idChambres($dateDebut, $dateFin, Connection $connection)
    {
        $dateDebut = $dateDebut->format('Y-m-d');
        $dateFin = $dateFin->format('Y-m-d');
        $sql = "
        SELECT chambre_id
        FROM reserver
        WHERE date_entree BETWEEN '$dateDebut' AND '$dateFin'
        ";
        $result = $connection->executeQuery($sql)->fetchAll();

        $idsChambres = array();

        foreach ($result as $key) {
            $idsChambres[] = $key['chambre_id'];
        }

        return $idsChambres;
    }
}

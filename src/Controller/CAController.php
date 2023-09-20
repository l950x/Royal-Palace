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

        $form = $this->createForm(DatesType::class, []);
        $form->handleRequest($request);

        // $dateDebut = '2023-09-17';
        // $dateFin = '2023-09-18';
        // $chiffreAffaires = 0;


        if ($form->isSubmitted()) {
            $data = $form->getData();
            $dateDebut = $data['dateEntree'];
            $dateFin = $data['dateSortie'];
            // $chiffreAffaires = $this->calculerCA($dateDebut, $dateFin, $connection);


            $idChambres = $this->idChambres($dateDebut, $dateFin, $connection);
            $chambres = $chambreRepository->findBy(['id' => $idChambres]);
            
            $CAT1 = $CAT2 = $CAT3 = 0;
            $cht1 = $cht2 = $cht3 = [];
            $total = 1;
            
            foreach ($chambres as $chambre) {
                $id = $chambre->getId();
                $reserv = $reserverRepository->findBy(['chambre' => $id]);
                $prixTotal = 0;
            
                foreach ($reserv as $cash) {
                    $prixTotal += $cash->getPrix();
                }
            
                switch ($chambre->getType()) {
                    case 1:
                        $CAT1 += $prixTotal;
                        $cht1[] = $chambre;
                        break;
                    case 2:
                        $CAT2 += $prixTotal;
                        $cht2[] = $chambre;
                        break;
                    case 3:
                        $CAT3 += $prixTotal;
                        $cht3[] = $chambre;
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
        } else {
            $dateDebut = new DateTimeImmutable('last year');
            $dateFin = new DateTimeImmutable('next year');
            
            $idChambres = $this->idChambres($dateDebut, $dateFin, $connection);
            $chambres = $chambreRepository->findBy(['id' => $idChambres]);
            
            $CAT1 = $CAT2 = $CAT3 = 0;
            $cht1 = $cht2 = $cht3 = [];
            
            foreach ($chambres as $chambre) {
                $id = $chambre->getId();
                $reserv = $reserverRepository->findBy(['chambre' => $id]);
                $prixTotal = 0;
            
                foreach ($reserv as $cash) {
                    $prixTotal += $cash->getPrix();
                }
            
                switch ($chambre->getType()) {
                    case 1:
                        $CAT1 += $prixTotal;
                        $cht1[] = $chambre;
                        break;
                    case 2:
                        $CAT2 += $prixTotal;
                        $cht2[] = $chambre;
                        break;
                    case 3:
                        $CAT3 += $prixTotal;
                        $cht3[] = $chambre;
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

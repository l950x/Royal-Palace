<?php

namespace App\Controller;

use App\Entity\Chambre;
use App\Form\ChambreType;
use App\Repository\ChambreRepository;
use App\Repository\ReserverRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/chambre')]
class ChambreController extends AbstractController
{
    #[Route('/liste/{id}', name: 'app_chambre_index', methods: ['GET'])]
    public function index(ReserverRepository $reserverRepository,ChambreRepository $chambreRepository, Request $request, SessionInterface $session): Response
    {
        // $session->set('price', null);
        // $session->set('dateEntree', null);
        // $session->set('dateSortie', null);
        $session->set('chambreId', null);
        // $session->set('nbPersonne', null);

        $id = $request->attributes->get('id');
        switch ($id) {
            case '1':
                $chambres = $chambreRepository->findBy(['Type' => 1]);

                foreach ($chambres as $chambre) {
                $reserver = $reserverRepository->findOneBy([
                    'chambre' => $id,
                    'validite' => 0,
                ]);

                if (!$reserver) {
                    $chambrelibres[] = $chambre;
                }
            }

                break;

            case '2':
                $chambres = $chambreRepository->findBy(['Type' => 2]);
                break;
            case '3':
                $chambres = $chambreRepository->findBy(['Type' => 3]);
                break;

            default:
                # code...
                break;
        }

        return $this->render('chambre/index.html.twig', [
            'chambres' => $chambrelibres,
        ]);
    }


    #[Route('/{id}', name: 'app_chambre_show', methods: ['GET'])]
    public function show(Chambre $chambre, Session $session): Response
    {
        $session->set('preChambre', $chambre->getId());
        return $this->render('chambre/show.html.twig', [
            'chambre' => $chambre,
            'numChambre' => $chambre->getId(),
        ]);
    }

    #[Route('/', name: 'app_chambre_category', methods: ['GET'])]
    public function category(ChambreRepository $chambreRepository): Response
    {
        return $this->render('chambre/categorie.html.twig', []);
    }

}

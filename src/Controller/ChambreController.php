<?php

namespace App\Controller;

use App\Entity\Chambre;
use App\Form\ChambreType;
use App\Repository\ChambreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/chambre')]
class ChambreController extends AbstractController
{
    #[Route('/liste/{id}', name: 'app_chambre_index', methods: ['GET'])]
    public function index(ChambreRepository $chambreRepository, Request $request, SessionInterface $session): Response
    {
        $session->set('price', null);
        $session->set('dateEntree', null);
        $session->set('dateSortie', null);
        $session->set('chambreId', null);
        $session->set('nbPersonne', null);

        $id = $request->attributes->get('id');
        switch ($id) {
            case '1':
                $chambres = $chambreRepository->findBy(['Type' => 1]);
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
            'chambres' => $chambres,
        ]);
    }


    #[Route('/{id}', name: 'app_chambre_show', methods: ['GET'])]
    public function show(Chambre $chambre): Response
    {
        return $this->render('chambre/show.html.twig', [
            'chambre' => $chambre,
        ]);
    }

    #[Route('/', name: 'app_chambre_category', methods: ['GET'])]
    public function category(ChambreRepository $chambreRepository): Response
    {
        return $this->render('chambre/categorie.html.twig', []);
    }

}

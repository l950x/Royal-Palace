<?php

namespace App\Controller;

use App\Entity\Reserver;
use App\Form\ReserverType;
use App\Repository\ReserverRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/reservation')]
class AdminReservationController extends AbstractController
{
    #[Route('/', name: 'app_admin_reservation_index', methods: ['GET'])]
    public function index(ReserverRepository $reserverRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        
        return $this->render('admin_reservation/index.html.twig', [
            'reservers' => $reserverRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reserver = new Reserver();
        $form = $this->createForm(ReserverType::class, $reserver);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reserver);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_reservation/new.html.twig', [
            'reserver' => $reserver,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_reservation_show', methods: ['GET'])]
    public function show(Reserver $reserver): Response
    {
        return $this->render('admin_reservation/show.html.twig', [
            'reserver' => $reserver,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reserver $reserver, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReserverType::class, $reserver);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_reservation/edit.html.twig', [
            'reserver' => $reserver,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reserver $reserver, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reserver->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reserver);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
}

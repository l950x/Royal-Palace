<?php

namespace App\Controller;

use App\Entity\Reserver;
use App\Form\DatesType;
use App\Repository\ReserverRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user,
        ]);
    }


    #[Route('/profile/reservations', name: 'app_profile_reservations')]
    public function reservations(ReserverRepository $reserverRepository): Response
    {
        $user = $this->getUser();
        $userId = $user->getId();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $reservations = $reserverRepository->findBy([
            'user' => $userId,
        ]);

        foreach ($reservations as $reservation) {
            $reservationEntree = [$reservation->getDateEntree()->format('Y-m-d')];
            $reservationSortie = [$reservation->getDateSortie()->format('Y-m-d')];
        }

        return $this->render('profile/reservations.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user,
            'reservations' => $reservations,
            'dateEntree' => $reservationEntree,
            'dateSortie' => $reservationSortie,
        ]);
    }



    #[Route('/profile/reservations/{id}', name: 'app_profile_reservations_edit')]
    public function reservationsEdit(ReserverRepository $reserverRepository,  Request $request): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $id = $request->attributes->get('id');

        $reservation = $reserverRepository->findOneBy([
            'id' => $id,
        ]);

        $formDates = $this->createForm(DatesType::class);
        $formDates->handleRequest($request);

        if ($formDates->isSubmitted() && $formDates->isValid()) {

            $data = $formDates->getData();
        }
        $dateEntree = $reservation->getDateEntree();
        $dateSortie = $reservation->getDateSortie();

        $dateEntreeFormat = $dateEntree->format('Y-m-d');
        $dateSortieFormat = $dateSortie->format('Y-m-d');
        return $this->render('profile/reservation.edit.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user,
            'reservation' => $reservation,
            'dateEntree' => $dateEntree,
            'dateSortie' => $dateSortie,
            'dateEntreeFormat' => $dateEntreeFormat,
            'dateSortieFormat' => $dateSortieFormat,
            'form' => $formDates,
        ]);
    }
}

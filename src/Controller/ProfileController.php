<?php

namespace App\Controller;

use App\Entity\Reserver;
use App\Entity\User;
use App\Form\DatesType;
use App\Repository\ReserverRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\PaiementType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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

    #[Route('/profile/infos', name: 'app_profile_infos')]
    public function infos(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('profile/infos.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profile/infos/edit', name: 'app_profile_edit')]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $hashPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashPassword);
            $entityManager->flush();

            return $this->redirectToRoute('app_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('profile/edit.html.twig', [
            'user' => $user,
            'form' => $form,
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

        $reservationEntree = [];
        $reservationSortie = [];

        foreach ($reservations as $reservation) {
            $reservationEntree[] = $reservation->getDateEntree()->format('d-m-Y');
            $reservationSortie[] = $reservation->getDateSortie()->format('d-m-Y');
        }

        if (count($reservations) > 0) {
            return $this->render('profile/reservations.html.twig', [
                'controller_name' => 'ProfileController',
                'user' => $user,
                'reservations' => $reservations,
                'dateEntree' => $reservationEntree,
                'dateSortie' => $reservationSortie,
            ]);
        } else {
            throw $this->createNotFoundException('Aucune réservation disponible (Vous avez oublié de réserver ?)');
        }
    }

    #[Route('/profile/reservations/{id}', name: 'app_profile_reservations_edit')]
    public function reservationsEdit(ReserverRepository $reserverRepository,  Request $request,  SessionInterface $session): Response
    {
        $session->set('price', null);
        $session->set('dateEntree', null);
        $session->set('dateSortie', null);
        $session->set('chambreId', null);
        $session->set('nbPersonne', null);



        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $id = $request->attributes->get('id');

        $reservation = $reserverRepository->findOneBy([
            'id' => $id,
        ]);

        $chambre = $reservation->getChambre();
        $formDates = $this->createForm(DatesType::class, [
            'dateEntree' => $reservation->getDateEntree(),
            'dateSortie' => $reservation->getDateSortie(),
        ]);

        $formDates->handleRequest($request);

        if ($formDates->isSubmitted()) {
            $data = $formDates->getData();
            $chambreId = $chambre->getId();
            $session->set('price', $chambre->getTarif());
            $session->set('dateEntree', $data['dateEntree']);
            $session->set('dateSortie', $data['dateSortie']);
            $session->set('nbPersonne', $reservation->getNbPersonne());
            $session->set('chambreId', $chambreId);

            return $this->redirectToRoute('app_paiement', ['edit' => 1]);
        }

        return $this->render('profile/reservation.edit.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user,
            'reservation' => $reservation,
            'form' => $formDates->createView(),

        ]);
    }
}

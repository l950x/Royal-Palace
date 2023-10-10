<?php

namespace App\Controller;

use App\Entity\Reserver;
use App\Form\ReserverType;
use App\Repository\ChambreRepository;
use App\Repository\ReserverRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Faker\Factory;
use Doctrine\DBAL\Connection;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin/reservation')]
class AdminReservationController extends AbstractController
{
    #[Route('/', name: 'app_admin_reservation_index', methods: ['GET'])]
    public function index(ReserverRepository $reserverRepository): Response
    {
        $user = $this->getUser();
        if (!$user || !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('admin_reservation/index.html.twig', [
            'reservers' => $reserverRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_reservation_new', methods: ['GET', 'POST'])]
    public function new(
        ChambreRepository $chambreRepository,
        UserRepository $userRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        Connection $connection,
        UserPasswordHasherInterface $userPasswordHasher
    ): Response {
        $form = $this->createFormBuilder()->add('NbReservation')->getForm();
        $form->handleRequest($request);
        $faker = Factory::create();

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $nb = $data['NbReservation'];

            $connection->executeQuery('DELETE FROM reserver');

            $users = $userRepository->findAll();
            $chambres = $chambreRepository->findAll();

            for ($i = 0; $i < $nb; $i++) {
                $reserver = new Reserver;

                $dateEntree = $faker->dateTimeBetween('now', '+1 month');
                $minDateSortie = clone $dateEntree;
                $minDateSortie->modify('+7 days');
                $dateSortie = $faker->dateTimeBetween($minDateSortie, '+3 months');
                $dateEntreeImmutable = new DateTimeImmutable('@' . $dateEntree->getTimestamp());
                $dateSortieImmutable = new DateTimeImmutable('@' . $dateSortie->getTimestamp());


                $randomUser = $users[array_rand($users)];
                $randomChambre = $chambres[array_rand($chambres)];
                $nbPersonne = random_int(1, 4);
                $price = $randomChambre->getTarif();
                $prixTotal = $price * $dateSortie->diff($dateEntree)->days * $nbPersonne;

                $reserver->setUser($randomUser)
                    ->setDateEntree($dateEntreeImmutable)
                    ->setDateSortie($dateSortieImmutable)
                    ->setChambre($randomChambre)
                    ->setPrix($prixTotal)
                    ->setNbPersonne($nbPersonne)
                    ->setValidite(0);

                $entityManager->persist($reserver);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_admin_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_reservation/new.html.twig', [
            'form' => $form->createView(),
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
        if ($this->isCsrfTokenValid('delete' . $reserver->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reserver);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
}

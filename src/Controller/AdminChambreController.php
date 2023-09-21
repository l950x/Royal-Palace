<?php

namespace App\Controller;

use App\Entity\Chambre;
use App\Entity\Hotel;
use App\Form\ChambreType;
use App\Repository\ChambreRepository;
use App\Repository\ReserverRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

#[Route('/admin/chambre')]
class AdminChambreController extends AbstractController
{
    #[Route('/', name: 'app_admin_chambre_index', methods: ['GET', 'POST'])]
    public function index(Request $request, ChambreRepository $chambreRepository, ReserverRepository $reserverRepository): Response
    {
        $user = $this->getUser();
        if (!$user || !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }

        $reservations = $reserverRepository->findAll();
        $chambresReserveesId = [];

        foreach ($reservations as $reservation) {
            $chambre = $reservation->getChambre();
            $chambresReserveesId[] = $chambre->getId();
        }

        $chambresReservees = $chambreRepository->findBy([
            'id' => $chambresReserveesId,
        ]);
        $chambres = $chambreRepository->findAll();

        $chambresLibres = [];

        foreach ($chambres as $chambre) {
            $chambreId = $chambre->getId();
            if (!in_array($chambreId, $chambresReserveesId)) {
                $chambresLibresId[] = $chambre;
                $chambresLibres = $chambreRepository->findBy([
                    'id' => $chambresLibresId,
                ]);
            }
        }


        $chambre1 = $chambreRepository->findBy(['Type' => 1]);
        $chambre2 = $chambreRepository->findBy(['Type' => 2]);
        $chambre3 = $chambreRepository->findBy(['Type' => 3]);

        $form = $this->createFormBuilder()
            ->add('choix', ChoiceType::class, [
                'choices' => [
                    'Reserver' => 'chambresReservees',
                    'Libre' => 'chambresLibres',
                    'Tout' => 'chambres',
                    'Categorie1' => 'chambres1',
                    'Categorie2' => 'chambres2',
                    'Categorie3' => 'chambres3',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Confirmer',
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $choice = $data['choix'];
            switch ($choice) {
                case 'chambresReservees':
                    $chambres = $chambresReservees;
                    break;
                case 'chambresLibres':
                    $chambres = $chambresLibres;
                    break;
                case 'chambres1':
                    $chambres = $chambre1;
                    break;
                case 'chambres2':
                    $chambres = $chambre2;
                    break;
                case 'chambres3':
                    $chambres = $chambre3;
                    break;
                case 'chambres':
                    break;
            }
        }

        return $this->render('admin_chambre/index.html.twig', [
            'chambres' => $chambres,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/new', name: 'app_admin_chambre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Connection $connection): Response
    {
        $chambre = new Chambre();

        $formChambreNew = $this->createFormBuilder()
            ->add('NbChambre')
            ->getForm();

        $formChambreNew->handleRequest($request);

        if ($formChambreNew->isSubmitted() && $formChambreNew->isValid()) {
            $data = $formChambreNew->getData();
            $nb = $data['NbChambre'];

            $connection->executeQuery('DELETE FROM reserver');
            $connection->executeQuery('DELETE FROM chambre');
            $connection->executeQuery('DELETE FROM hotel');

            $hotel = new Hotel();
            $hotel->setNom('Royal Palaces');
            $hotel->setNbChambre(50);

            for ($i = 0; $i < $nb; $i++) {
                $chambre = new Chambre();
                $categorie = random_int(1, 3);
                $chambre->setType($categorie);
                $chambre->setVueSurMer(random_int(0, 1));
                $chambre->setTelephone(random_int(0, 1));
                $chambre->setTelevisionEcranPlat(random_int(0, 1));
                $chambre->setClimatisation(random_int(0, 1));
                $chambre->setHotel($hotel);
                switch ($categorie) {
                    case '1':
                        $chambre->setTarif(random_int(8500, 10000));
                        $chambre->setSuperficie(random_int(25, 50));

                        $chambre->setChaineALaCarte(1);
                        $chambre->setChaineSatellite(1);
                        $chambre->setChaineDuCable(1);
                        $chambre->setCoffreFort(1);
                        $chambre->setMaterielDeRepassage(1);
                        $chambre->setWifiGratuit(1);
                        break;
                    case '2':
                        $chambre->setTarif(random_int(5725, 8000));
                        $chambre->setSuperficie(random_int(12, 25));
                        $chambre->setChaineALaCarte(1);
                        $chambre->setChaineSatellite(random_int(0, 1));
                        $chambre->setChaineDuCable(1);
                        $chambre->setCoffreFort(random_int(0, 1));
                        $chambre->setMaterielDeRepassage(1);
                        $chambre->setWifiGratuit(1);

                        break;
                    case '3':
                        $chambre->setTarif(random_int(3250, 5000));
                        $chambre->setSuperficie(random_int(6, 12));
                        $chambre->setChaineALaCarte(1);
                        $chambre->setChaineSatellite(random_int(0, 1));
                        $chambre->setChaineDuCable(1);
                        $chambre->setCoffreFort(random_int(0, 1));
                        $chambre->setMaterielDeRepassage(1);
                        $chambre->setWifiGratuit(random_int(0, 1));

                        break;

                    default:
                        echo "Erreur";
                        break;
                }
                $entityManager->persist($chambre);
                $entityManager->flush();
            }


            return $this->redirectToRoute('app_admin_chambre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_chambre/new.html.twig', [
            'chambre' => $chambre,
            'form' => $formChambreNew,


        ]);
    }

    #[Route('/{id}', name: 'app_admin_chambre_show', methods: ['GET'])]
    public function show(Chambre $chambre): Response
    {
        return $this->render('admin_chambre/show.html.twig', [
            'chambre' => $chambre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_chambre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Chambre $chambre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChambreType::class, $chambre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_chambre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_chambre/edit.html.twig', [
            'chambre' => $chambre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_chambre_delete', methods: ['POST'])]
    public function delete(Request $request, Chambre $chambre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $chambre->getId(), $request->request->get('_token'))) {
            $entityManager->remove($chambre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_chambre_index', [], Response::HTTP_SEE_OTHER);
    }
}

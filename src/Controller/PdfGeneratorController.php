<?php
 
namespace App\Controller;

use App\Repository\ReserverRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PdfGeneratorController extends AbstractController
{
    #[Route('/paiement/facture', name: 'app_pdf_generator')]
    public function index(SessionInterface $session, ReserverRepository $reserverRepository): Response
    {
        // return $this->render('pdf_generator/index.html.twig', [
        //     'controller_name' => 'PdfGeneratorController',
        // ]);
        $user = $this->getUser();

        $reservation = $reserverRepository->findAll();

        $nbReservation = $session->get('reservationId');
        $price = $session->get('price');
        $priceFormat = number_format($price, 2, ',', ' ');
        $nbPersonne = $session->get('nbPersonne');
        $dateEntree = $session->get('dateEntree');
        $dateEntreeFormat = $dateEntree->format('Y-m-d');
        $dateSortie = $session->get('dateSortie');
        $dateSortieFormat = $dateSortie->format('Y-m-d');
        $duree = date_diff($dateEntree, $dateSortie);
        $dureeFormat = "$duree->y ans, $duree->m mois, $duree->d jours";
        $prixTotal = $price * $dateSortie->diff($dateEntree)->days * $nbPersonne;
        $prixTotalFormat = number_format($prixTotal, 2, ',', ' ');
        
        $data = [
            'nbReservation'=> $nbReservation,
            'imageSrc'     => $this->imageToBase64($this->getParameter('kernel.project_dir') . '/public/images/profile.png'),
            'nom'          => $user->getNom(),
            'prenom'       => $user->getPrenom(),
            'adresse'      => $user->getAdresse(),
            'email'        => $user->getEmail(),
            'chambre'      => $session->get('chambreId'),
            'prixChambre'  => $priceFormat,
            'prixTotal'    => $prixTotalFormat,
            'nbPersonne'   => $nbPersonne,
            'dateEntree'   => $dateEntreeFormat,
            'dateSortie'   => $dateSortieFormat,
            'dureeSejour'  => $dureeFormat
        ];
        $html =  $this->renderView('pdf_generator/index.html.twig', $data);
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();
         
        return new Response (
            $dompdf->stream('resume', ["Attachment" => false]),
            Response::HTTP_OK,
            ['Content-Type' => 'application/pdf']
        );
    }
 
    private function imageToBase64($path) {
        $path = $path;
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }
}

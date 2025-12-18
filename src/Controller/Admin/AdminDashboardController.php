<?php

namespace App\Controller\Admin;

use App\Repository\ArtistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function index(ArtistRepository $artistRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Récupération des statistiques via le repository
        $stats = $artistRepository->getStatistics();

        // Passage des variables au template
        return $this->render('admin/dashboard.html.twig', $stats);
    }
}

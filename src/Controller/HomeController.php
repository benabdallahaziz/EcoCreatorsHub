<?php
// src/Controller/HomeController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
{

    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $em): Response
    {
        // Récupérer les vrais artistes certifiés
        $featuredArtists = $em->getRepository(\App\Entity\Artist::class)
            ->findBy(['isCertified' => true], ['id' => 'DESC'], 6);

        // Récupérer les journaux récents publiés
        $recentJournals = $em->getRepository(\App\Entity\CreationJournal::class)
            ->findPublishedJournals(0, 6);

        // Statistiques
        $stats = [
            'totalArtists' => $em->getRepository(\App\Entity\Artist::class)->count([]),
            'totalJournals' => $em->getRepository(\App\Entity\CreationJournal::class)->countPublished(),
            'certifiedArtists' => $em->getRepository(\App\Entity\Artist::class)->count(['isCertified' => true]),
        ];

        return $this->render('home/index.html.twig', [
            'featuredArtists' => $featuredArtists,
            'recentJournals' => $recentJournals,
            'stats' => $stats,
        ]);
    }

    // Routes temporaires pour le développement

    #[Route('/artists', name: 'app_artists')]
    public function artists(): Response
    {
        return $this->render('temporary/artists.html.twig', [
            'message' => 'Page Artistes - En développement',
            'page_title' => '🎨 Artistes Écologiques'
        ]);
    }

    #[Route('/journals', name: 'app_journals')]
    public function journals(): Response
    {
        // Redirect to the real journals page (CreationJournalController index)
        return $this->redirectToRoute('journal_index');
    }

    #[Route('/materials', name: 'app_materials')]
    public function materials(): Response
    {
        return $this->render('temporary/materials.html.twig', [
            'message' => 'Page Matériaux Écologiques - En développement',
            'page_title' => '🎨 Catalogue de Matériaux'
        ]);
    }

    #[Route('/tutorials', name: 'app_tutorials')]
    public function tutorials(): Response
    {
        return $this->render('temporary/tutorials.html.twig', [
            'message' => 'Page Tutoriels - En développement',
            'page_title' => '📚 Tutoriels Écologiques'
        ]);
    }



    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('temporary/about.html.twig', [
            'message' => 'Page À Propos - En développement',
            'page_title' => 'ℹ️ À Propos'
        ]);
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('temporary/contact.html.twig', [
            'message' => 'Page Contact - En développement',
            'page_title' => '📧 Contact'
        ]);
    }

    #[Route('/shop', name: 'app_shop')]
    public function shop(): Response
    {
        return $this->render('temporary/shop.html.twig', [
            'message' => 'Page Boutique - En développement',
            'page_title' => '🛍️ Boutique'
        ]);
    }

    #[Route('/cart', name: 'app_cart')]
    public function cart(): Response
    {
        return $this->render('temporary/cart.html.twig', [
            'message' => 'Page Panier - En développement',
            'page_title' => '🛒 Panier'
        ]);
    }
}
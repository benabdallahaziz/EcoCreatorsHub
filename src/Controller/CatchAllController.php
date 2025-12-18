<?php
// src/Controller/CatchAllController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CatchAllController extends AbstractController
{
    /**
     * Route catch-all pour les URL inexistantes
     *
     * - priority: -1 pour s'assurer que les autres routes sont évaluées avant
     * - requirements: '.+' capture toutes les routes non vides
     */
    #[Route('/{url}', name: 'catch_all', requirements: ['url' => '.+'], priority: -1)]
    public function catchAll(): RedirectResponse
    {
        // Option 1 : rediriger vers la page d'accueil
        return $this->redirectToRoute('app_home');

        // Option 2 : afficher une page 404 personnalisée
        // return $this->render('bundles/TwigBundle/Exception/error404.html.twig', [], new Response('', 404));
    }
}

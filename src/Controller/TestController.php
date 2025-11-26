<?php

namespace App\Controller;

use App\Entity\Artist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupérer tous les artistes
        $artists = $entityManager->getRepository(Artist::class)->findAll();

        return $this->render('test/index.html.twig', [
            'artists' => $artists
        ]);
    }

    #[Route('/test/create-artist', name: 'test_create_artist')]
    public function createArtist(EntityManagerInterface $entityManager): Response
    {
        $artist = new Artist();
        $artist->setName('Test Artist ' . rand(100, 999));
        $artist->setBio('Artiste créé via test - ' . date('H:i:s'));
        $artist->setEcoTechnique('Technique de test écologique');
        $artist->setIsCertified(rand(0, 1) === 1);
        $artist->setCreatedAt(new \DateTime());

        $entityManager->persist($artist);
        $entityManager->flush();

        return $this->render('test/create.html.twig', [
            'artist' => $artist
        ]);
    }
}
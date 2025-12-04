<?php

namespace App\Controller;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Artist;
use App\Entity\User;
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
 public function createArtist(
     EntityManagerInterface $entityManager,
     UserPasswordHasherInterface $passwordHasher
 ): Response {
     // Create a new user
     $user = new User();
     $uniqueId = rand(10000, 99999);
     $user->setUsername('artist_user_' . $uniqueId);
     $user->setEmail('artist' . $uniqueId . '@example.com');
     $user->setPassword($passwordHasher->hashPassword($user, 'password123'));
     $user->setRoles(['ROLE_USER']);
     // Add any other required fields for your User entity

     $entityManager->persist($user);
     $entityManager->flush();

     // Create the artist
     $artist = new Artist();
     $artist->setName('Test Artist ' . $uniqueId);
     $artist->setBio('Artiste créé via test - ' . date('H:i:s'));
     $artist->setEcoTechnique('Technique de test écologique');
     $artist->setIsCertified(rand(0, 1) === 1);
     $artist->setCreatedAt(new \DateTime());
     $artist->setUser($user);

     $entityManager->persist($artist);
     $entityManager->flush();

     return $this->render('test/create.html.twig', [
         'artist' => $artist,
         'user' => $user
     ]);
 }
}
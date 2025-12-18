<?php
// src/Controller/User/ProfileController.php

namespace App\Controller\User;

use App\Entity\User;
use App\Entity\Artist;
use App\Form\UserProfileType;
use App\Form\ArtistProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/profile')]
#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    #[Route('', name: 'user_profile', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // Formulaire utilisateur
        $userForm = $this->createForm(UserProfileType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $plainPassword = $userForm->get('plainPassword')->getData();
            if ($plainPassword) {
                $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            }
            $em->flush();
            $this->addFlash('success', 'Profil utilisateur mis à jour !');
            return $this->redirectToRoute('user_profile');
        }

        // Formulaire artiste (si l'utilisateur est artiste)
        $artistForm = null;
        if ($user->isArtist()) {
            // Récupérer l'artist existant ou en créer un nouveau
            $artist = $user->getArtist() ?? new Artist();
            $artist->setUser($user);

            $artistForm = $this->createForm(ArtistProfileType::class, $artist);
            $artistForm->handleRequest($request);

            if ($artistForm->isSubmitted() && $artistForm->isValid()) {
                // Si bio vide, mettre à null
                if ($artist->getBio() === '') {
                    $artist->setBio(null);
                }

                $em->persist($artist);
                $em->flush();
                $this->addFlash('success', 'Profil artiste mis à jour !');
                return $this->redirectToRoute('user_profile');
            }
        }

        return $this->render('user/profile_edit.html.twig', [
            'userForm' => $userForm->createView(),
            'artistForm' => $artistForm ? $artistForm->createView() : null,
        ]);
    }
}

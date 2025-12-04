<?php

namespace App\Controller;

use App\Entity\CertificationRequest;
use App\Form\CertificationRequestType;
use App\Repository\CertificationRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/artist_certification')]
#[IsGranted('ROLE_ARTIST')] // Seuls les artistes connectés peuvent accéder
class ArtistCertificationController extends AbstractController
{
    // Route pour accéder à /artist_certification → redirige vers /request
    #[Route('/', name: 'artist_certification_index')]
    public function index(): Response
    {
        return $this->redirectToRoute('artist_certification_request');
    }

    // Formulaire pour envoyer une demande de certification
    #[Route('/request', name: 'artist_certification_request')]
    public function requestCertif(
        Request $request,
        CertificationRequestRepository $repo,
        EntityManagerInterface $em
    ): Response {

        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté.');
        }

        $artist = $user->getArtistProfile();
        if (!$artist) {
            throw $this->createAccessDeniedException('Profil artiste non trouvé.');
        }

        // Vérifier s'il existe déjà une demande en cours
        $existing = $repo->findOneBy(['artist' => $artist, 'status' => 'pending']);
        if ($existing) {
            return $this->redirectToRoute('artist_certification_status');
        }

        $certif = new CertificationRequest();
        $certif->setArtist($artist);

        $form = $this->createForm(CertificationRequestType::class, $certif);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($certif);
            $em->flush();

            $this->addFlash('success', 'Votre demande a été envoyée avec succès.');
            return $this->redirectToRoute('artist_certification_status');
        }

        return $this->render('artist_certification/request.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Page pour voir le statut de la demande
    #[Route('/status', name: 'artist_certification_status')]
    public function status(CertificationRequestRepository $repo): Response
    {
        $user = $this->getUser();
        $artist = $user->getArtistProfile();

        $request = $repo->findOneBy(
            ['artist' => $artist],
            ['createdAt' => 'DESC']
        );

        return $this->render('artist_certification/status.html.twig', [
            'request' => $request,
        ]);
    }

    // Modifier une demande si elle est toujours pending
    #[Route('/edit/{id}', name: 'artist_certification_edit')]
    public function edit(
        CertificationRequest $certif,
        Request $request,
        EntityManagerInterface $em
    ): Response {

        $user = $this->getUser();
        $artist = $user->getArtistProfile();

        // Sécurité : l'artiste ne peut éditer que sa propre demande
        if ($certif->getArtist() !== $artist) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cette demande.');
        }

        if ($certif->getStatus() !== 'pending') {
            $this->addFlash('warning', 'Vous ne pouvez modifier que les demandes en attente.');
            return $this->redirectToRoute('artist_certification_status');
        }

        $form = $this->createForm(CertificationRequestType::class, $certif);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $certif->setUpdatedAt(new \DateTime());
            $em->flush();

            $this->addFlash('success', 'Votre demande a été mise à jour.');
            return $this->redirectToRoute('artist_certification_status');
        }

        return $this->render('artist_certification/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

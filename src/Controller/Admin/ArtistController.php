<?php
// src/Controller/Admin/ArtistController.php

namespace App\Controller\Admin;

use App\Entity\Artist;
use App\Form\AdminArtistType;
use App\Repository\ArtistRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/admin/artist')]
#[IsGranted('ROLE_ADMIN')]
class ArtistController extends AbstractController
{
    private const ITEMS_PER_PAGE = 15;

    // =========================
    // LISTE DES ARTISTES
    // =========================
    #[Route('/', name: 'app_admin_artist_index', methods: ['GET'])]
    public function index(Request $request, ArtistRepository $artistRepository): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $search = $request->query->get('search', '');
        $certified = $request->query->get('certified', '');

        $qb = $artistRepository->createQueryBuilder('a')
            ->leftJoin('a.user', 'u')
            ->addSelect('u')
            ->orderBy('a.createdAt', 'DESC');

        if ($search) {
            $qb->andWhere('a.name LIKE :search OR u.email LIKE :search OR u.username LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        if ($certified !== '') {
            $qb->andWhere('a.isCertified = :certified')
               ->setParameter('certified', $certified === 'true');
        }

        $totalResults = count($qb->getQuery()->getResult());
        $totalPages = max(1, ceil($totalResults / self::ITEMS_PER_PAGE));

        $qb->setFirstResult(($page - 1) * self::ITEMS_PER_PAGE)
           ->setMaxResults(self::ITEMS_PER_PAGE);

        return $this->render('admin/artist/index.html.twig', [
            'artists' => $qb->getQuery()->getResult(),
            'current_page' => $page,
            'total_pages' => $totalPages,
            'search' => $search,
            'certified' => $certified,
            'total_artists' => $artistRepository->count([]),
            'certified_artists' => $artistRepository->count(['isCertified' => true]),
            'paginator_total' => $totalResults,
        ]);
    }

    // =========================
    // CRÉATION D'UN ARTISTE
    // =========================
    #[Route('/new', name: 'app_admin_artist_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        UserRepository $userRepository
    ): Response {
        $artist = new Artist();
        $availableUsers = $userRepository->findUsersWithoutArtist();

        $form = $this->createForm(AdminArtistType::class, $artist, [
            'available_users' => $availableUsers,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($artist);
            $em->flush();

            $this->addFlash('success', 'Artiste créé avec succès');

            // ✅ REDIRECTION OBLIGATOIRE
            return $this->redirectToRoute('app_admin_artist_index');
        }

        return $this->render('admin/artist/new.html.twig', [
            'form' => $form->createView(),
            'available_users_count' => count($availableUsers),
        ]);
    }
// =========================
// ÉDITION D'UN ARTISTE
// =========================
#[Route('/{id}/edit', name: 'app_admin_artist_edit', methods: ['GET', 'POST'])]
public function edit(
    Request $request,
    Artist $artist,
    EntityManagerInterface $em,
    UserRepository $userRepository
): Response {
    // utilisateurs disponibles + celui déjà lié
    $availableUsers = $userRepository->findUsersWithoutArtist();
    if ($artist->getUser()) {
        $availableUsers[] = $artist->getUser();
    }

    $form = $this->createForm(AdminArtistType::class, $artist, [
        'available_users' => $availableUsers,
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->flush();

        $this->addFlash('success', 'Artiste modifié avec succès');

        return $this->redirectToRoute('app_admin_artist_index');
    }

    return $this->render('admin/artist/edit.html.twig', [
        'artist' => $artist,
        'form' => $form->createView(),
        'available_users_count' => count($availableUsers),
    ]);
}

    // =========================
    // TOGGLE CERTIFICATION
    // =========================
    #[Route('/{id}/toggle-certification', name: 'app_admin_artist_toggle_certification', methods: ['POST'])]
    public function toggleCertification(
        Request $request,
        Artist $artist,
        EntityManagerInterface $em
    ): Response {
        if (!$this->isCsrfTokenValid('toggle-certification'.$artist->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirectToRoute('app_admin_artist_index');
        }

        $artist->setIsCertified(!$artist->isIsCertified());
        $em->flush();

        $this->addFlash(
            'success',
            $artist->isIsCertified()
                ? 'Artiste certifié avec succès'
                : 'Certification retirée'
        );

        return $this->redirectToRoute('app_admin_artist_index');
    }
#[Route('/{id}', name: 'app_admin_artist_show', methods: ['GET'])]
public function show(Artist $artist): Response
{
    return $this->render('admin/artist/show.html.twig', [
        'artist' => $artist,
    ]);
}
#[Route('/{id}/delete', name: 'app_admin_artist_delete', methods: ['POST'])]
public function delete(
    Request $request,
    Artist $artist,
    EntityManagerInterface $entityManager
): Response {
    if (!$this->isCsrfTokenValid('delete' . $artist->getId(), $request->request->get('_token'))) {
        $this->addFlash('error', 'Token CSRF invalide.');
        return $this->redirectToRoute('app_admin_artist_index');
    }

    try {
        // Récupérer l'utilisateur lié à l'artiste
        $user = $artist->getUser();
        if ($user) {
            $roles = $user->getRoles();
            if (($key = array_search('ROLE_ARTIST', $roles)) !== false) {
                unset($roles[$key]);
                $user->setRoles(array_values($roles)); // réindexer le tableau
            }
        }

        // Supprimer le profil artiste
        $entityManager->remove($artist);
        $entityManager->flush();

        $this->addFlash('success', 'Artiste supprimé et rôle ROLE_ARTIST retiré de l’utilisateur.');
    } catch (\Exception $e) {
        $this->addFlash('error', 'Erreur lors de la suppression : '.$e->getMessage());
    }

    // Redirection vers la liste des utilisateurs pour voir la mise à jour
    return $this->redirectToRoute('app_admin_user_index');
}


}

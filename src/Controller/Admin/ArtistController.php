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

    #[Route('/', name: 'app_admin_artist_index', methods: ['GET'])]
    public function index(Request $request, ArtistRepository $artistRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $search = $request->query->get('search', '');
        $certified = $request->query->get('certified', '');

        // Créer le QueryBuilder
      $queryBuilder = $artistRepository->createQueryBuilder('a')
          ->leftJoin('a.user', 'u')  // JOIN obligatoire
          ->addSelect('u')           // hydrate l'utilisateur
          ->orderBy('a.createdAt', 'DESC');

      if ($search) {
          $queryBuilder
              ->andWhere('a.name LIKE :search OR u.email LIKE :search OR u.username LIKE :search')
              ->setParameter('search', '%' . $search . '%');
      }


       if ($certified !== '') {
           $queryBuilder
               ->andWhere('a.isCertified = :certified')
               ->setParameter('certified', $certified === 'true');
       }


        // Pagination manuelle
        $totalResults = count($queryBuilder->getQuery()->getResult());
        $totalPages = ceil($totalResults / self::ITEMS_PER_PAGE);
        $queryBuilder
            ->setFirstResult(($page - 1) * self::ITEMS_PER_PAGE)
            ->setMaxResults(self::ITEMS_PER_PAGE);

        $artists = $queryBuilder->getQuery()->getResult();

        return $this->render('admin/artist/index.html.twig', [
            'artists' => $artists, // tableau simple
            'current_page' => $page,
            'total_pages' => $totalPages,
            'search' => $search,
            'certified' => $certified,
            'total_artists' => $artistRepository->count([]),
            'certified_artists' => $artistRepository->count(['isCertified' => true]),
            'paginator_total' => $totalResults,
        ]);
    }

   #[Route('/new', name: 'app_admin_artist_new', methods: ['GET', 'POST'])]
   public function new(
       Request $request,
       EntityManagerInterface $entityManager,
       UserRepository $userRepository
   ): Response
   {
       $artist = new Artist();

       $availableUsers = $userRepository->findUsersWithoutArtist();

       if (empty($availableUsers)) {
           $this->addFlash('warning', 'Aucun utilisateur disponible pour créer un artiste.');
           return $this->redirectToRoute('app_admin_artist_index');
       }

       $form = $this->createForm(AdminArtistType::class, $artist, [
           'available_users' => $availableUsers
       ]);

       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid()) {
           $entityManager->persist($artist);
           $entityManager->flush();

           $this->addFlash('success', 'Artiste créé avec succès.');
           return $this->redirectToRoute('app_admin_artist_index');
       } elseif ($form->isSubmitted()) {
           $this->addFlash('error', 'Le formulaire contient des erreurs. Veuillez vérifier les champs.');
       }

       return $this->render('admin/artist/new.html.twig', [
           'artist' => $artist,
           'form' => $form->createView(),
           'available_users_count' => count($availableUsers)
       ]);
   }


    #[Route('/{id}', name: 'app_admin_artist_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Artist $artist): Response
    {
        return $this->render('admin/artist/show.html.twig', [
            'artist' => $artist,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_artist_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Artist $artist,
        EntityManagerInterface $entityManager,
        ArtistRepository $artistRepository,
        UserRepository $userRepository
    ): Response
    {
        // Obtenir les utilisateurs disponibles pour l'édition
        $availableUsers = $userRepository->findAvailableUsersForEdit($artist->getUser());

        $form = $this->createForm(AdminArtistType::class, $artist, [
            'available_users' => $availableUsers
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();
                $this->addFlash('success', 'Artiste modifié avec succès.');
                return $this->redirectToRoute('app_admin_artist_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la modification: ' . $e->getMessage());
            }
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Le formulaire contient des erreurs. Veuillez vérifier les champs.');
        }

        return $this->render('admin/artist/edit.html.twig', [
            'artist' => $artist,
            'form' => $form->createView(),
            'available_users_count' => count($availableUsers)
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_artist_delete', methods: ['POST'])]
    public function delete(Request $request, Artist $artist, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$artist->getId(), $request->request->get('_token'))) {
            try {
                // Vérifier si l'artiste a du contenu associé
                $hasContent = $artist->getCreationJournals()->count() > 0
                    || $artist->getTutorials()->count() > 0
                    || $artist->getCertificationRequests()->count() > 0;

                if ($hasContent) {
                    $this->addFlash('warning',
                        'Cet artiste a du contenu associé (journaux, tutoriels, demandes de certification). ' .
                        'Supprimez d\'abord le contenu associé avant de supprimer l\'artiste.'
                    );
                    return $this->redirectToRoute('app_admin_artist_index');
                }

                $entityManager->remove($artist);
                $entityManager->flush();

                $this->addFlash('success', 'Artiste supprimé avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la suppression: ' . $e->getMessage());
            }
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('app_admin_artist_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/toggle-certification', name: 'app_admin_artist_toggle_certification', methods: ['POST'])]
    public function toggleCertification(Request $request, Artist $artist, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('toggle-certification'.$artist->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_admin_artist_index');
        }

        try {
            $newStatus = !$artist->isIsCertified();
            $artist->setIsCertified($newStatus);
            $entityManager->flush();

            $message = $newStatus
                ? 'Artiste certifié avec succès.'
                : 'Certification retirée avec succès.';

            $this->addFlash('success', $message);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors du changement de certification: ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_admin_artist_index');
    }

    #[Route('/stats', name: 'app_admin_artist_stats', methods: ['GET'])]
    public function stats(ArtistRepository $artistRepository): Response
    {
        $stats = $artistRepository->getStatistics();

        return $this->render('admin/artist/stats.html.twig', [
            'stats' => $stats,
        ]);
    }

    /**
     * Version alternative de l'index si la pagination ne fonctionne pas
     * Route alternative pour debug
     */
    #[Route('/debug', name: 'app_admin_artist_debug', methods: ['GET'])]
    public function debugIndex(Request $request, ArtistRepository $artistRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $search = $request->query->get('search', '');
        $certified = $request->query->get('certified', '');

        // Méthode simple sans paginator complexe
        $offset = ($page - 1) * self::ITEMS_PER_PAGE;

        // Créer la requête
        $queryBuilder = $artistRepository->createQueryBuilder('a')
            ->leftJoin('a.user', 'u')
            ->addSelect('u')
            ->orderBy('a.createdAt', 'DESC');

        // Appliquer les filtres
        if ($search) {
            $queryBuilder
                ->andWhere('a.name LIKE :search OR u.email LIKE :search OR u.username LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if ($certified !== '') {
            $queryBuilder
                ->andWhere('a.isCertified = :certified')
                ->setParameter('certified', $certified === 'true');
        }

        // Récupérer tous les résultats
        $allArtists = $queryBuilder->getQuery()->getResult();

        // Pagination manuelle
        $totalResults = count($allArtists);
        $totalPages = ceil($totalResults / self::ITEMS_PER_PAGE);
        $artists = array_slice($allArtists, $offset, self::ITEMS_PER_PAGE);

        // Debug info
        $debugInfo = [
            'total_results' => $totalResults,
            'artists_count' => count($artists),
            'all_artists_count' => count($allArtists),
            'page' => $page,
            'offset' => $offset,
            'limit' => self::ITEMS_PER_PAGE,
            'total_pages' => $totalPages,
            'search' => $search,
            'certified' => $certified,
        ];

        return $this->render('admin/artist/debug.html.twig', [
            'artists' => $artists,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'search' => $search,
            'certified' => $certified,
            'total_artists' => $artistRepository->count([]),
            'certified_artists' => $artistRepository->count(['isCertified' => true]),
            'debug_info' => $debugInfo,
        ]);
    }
}
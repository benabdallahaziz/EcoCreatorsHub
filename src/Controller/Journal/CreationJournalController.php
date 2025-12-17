<?php

namespace App\Controller\Journal;

use App\Entity\CreationJournal;
use App\Entity\CreationStep;
use App\Form\CreationJournalType;
use App\Form\CreationStepType;
use App\Repository\CreationJournalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\FileUploader;

use App\Service\EmailService;
use App\Service\PdfService;
use App\Service\TranslationService;
use App\Service\ModerationService;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/journal')]
class CreationJournalController extends AbstractController
{
    #[Route('/', name: 'journal_index', methods: ['GET'])]
    public function index(Request $request, CreationJournalRepository $creationJournalRepository, EntityManagerInterface $em): Response
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = 6;
        $offset = ($page - 1) * $perPage;
        $sort = $request->query->get('sort', 'recent');
        $category = $request->query->get('category');
        $query = $request->query->get('q', '');
        
        // If search query exists, only show search results
        if (!empty($query)) {
            $journals = $creationJournalRepository->searchPublishedJournals($query, $offset, $perPage);
            $total = $creationJournalRepository->countSearchResults($query);
        } elseif ($this->getUser() && in_array('ROLE_ARTIST', $this->getUser()->getRoles())) {
            $artist = $this->getUser()->getArtist();
            if ($artist) {
                $journals = $creationJournalRepository->findBy(
                    ['artist' => $artist],
                    ['id' => 'DESC'],
                    $perPage,
                    $offset
                );
                $total = $creationJournalRepository->count(['artist' => $artist]);
            } else {
                $journals = [];
                $total = 0;
            }
        } else {
            $journals = $creationJournalRepository->findPublishedWithFilters($offset, $perPage, $sort, $category);
            $total = $creationJournalRepository->countPublishedWithFilters($category);
        }
        
        // Get real likes and views counts
        $journalStats = [];
        foreach ($journals as $journal) {
            $journalStats[$journal->getId()] = [
                'likes' => $em->getRepository(\App\Entity\JournalLike::class)->count(['creationJournal' => $journal]),
                'views' => $em->getRepository(\App\Entity\JournalView::class)->count(['journal' => $journal])
            ];
        }
        
        $totalPages = ceil($total / $perPage);
        $categories = $em->getRepository(\App\Entity\JournalCategory::class)->findAll();

        return $this->render('journal/creation_journal/index.html.twig', [
            'creation_journals' => $journals,
            'journal_stats' => $journalStats,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_items' => $total,
            'per_page' => $perPage,
            'categories' => $categories,
            'current_sort' => $sort,
            'current_category' => $category,
        ]);
    }

    #[Route('/new', name: 'journal_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader, ModerationService $moderationService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ARTIST');

        $creationJournal = new CreationJournal();
        $user = $this->getUser();
        if ($user && $user->getArtist()) {
            $creationJournal->setArtist($user->getArtist());
        }
        $creationJournal->setCreatedAt(new \DateTime());

        $form = $this->createForm(CreationJournalType::class, $creationJournal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check for bad words
            $content = $creationJournal->getTitle() . ' ' . $creationJournal->getDescription();
            $artistName = $creationJournal->getArtist() ? $creationJournal->getArtist()->getName() : 'Inconnu';
            
            if ($moderationService->checkContent($content, $artistName, $creationJournal->getTitle())) {
                $this->addFlash('warning', 'Votre contenu est en cours de modération.');
            }
            
            $uploadedFiles = $form->get('images')->getData();
            $images = $creationJournal->getImages() ?? [];
            if ($uploadedFiles) {
                foreach ($uploadedFiles as $file) {
                    if ($file instanceof UploadedFile) {
                        $images[] = $fileUploader->upload($file);
                    }
                }
            }
            $creationJournal->setImages($images);

            $entityManager->persist($creationJournal);
            $entityManager->flush();

            $this->addFlash('success', 'Votre journal de création a été créé avec succès !');
            return $this->redirectToRoute('journal_show', ['id' => $creationJournal->getId()]);
        }

        return $this->render('journal/creation_journal/new.html.twig', [
            'creation_journal' => $creationJournal,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'journal_show', methods: ['GET'])]
    public function show(CreationJournal $creationJournal, EntityManagerInterface $em): Response
    {
        if (!$creationJournal->getIsPublished()) {
            $user = $this->getUser();
            $artist = $creationJournal->getArtist();
            if (!($user && $artist && $artist->getUser() === $user)) {
                throw $this->createAccessDeniedException('Ce journal n\'est pas publié.');
            }
        }

        $user = $this->getUser();
        $userLiked = false;
        $totalLikes = 0;
        $totalViews = 0;

        if ($user) {
            $userLiked = $em->getRepository(\App\Entity\JournalLike::class)
                ->findOneBy(['user' => $user, 'creationJournal' => $creationJournal]) !== null;
        }

        $totalLikes = $em->getRepository(\App\Entity\JournalLike::class)
            ->count(['creationJournal' => $creationJournal]);
        
        $totalViews = $em->getRepository(\App\Entity\JournalView::class)
            ->count(['journal' => $creationJournal]);

        return $this->render('journal/creation_journal/show.html.twig', [
            'creation_journal' => $creationJournal,
            'steps' => $creationJournal->getSteps(),
            'user_liked' => $userLiked,
            'total_likes' => $totalLikes,
            'total_views' => $totalViews,
        ]);
    }

    #[Route('/{id}/edit', name: 'journal_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CreationJournal $creationJournal, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $user = $this->getUser();
        if (!($user && $creationJournal->getArtist() && $creationJournal->getArtist()->getUser() === $user)) {
            throw $this->createAccessDeniedException('You do not have permission to edit this journal.');
        }

        $form = $this->createForm(CreationJournalType::class, $creationJournal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFiles = $form->get('images')->getData();
            $images = $creationJournal->getImages() ?? [];
            if ($uploadedFiles) {
                foreach ($uploadedFiles as $file) {
                    if ($file instanceof UploadedFile) {
                        $images[] = $fileUploader->upload($file);
                    }
                }
            }
            $creationJournal->setImages($images);
            $creationJournal->setUpdatedAt(new \DateTime());
            $entityManager->flush();

            $this->addFlash('success', 'Votre journal a été mis à jour.');
            return $this->redirectToRoute('journal_show', ['id' => $creationJournal->getId()]);
        }

        return $this->render('journal/creation_journal/edit.html.twig', [
            'creation_journal' => $creationJournal,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'journal_delete', methods: ['POST'])]
    public function delete(Request $request, CreationJournal $creationJournal, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $isOwner = $user && $creationJournal->getArtist() && $creationJournal->getArtist()->getUser() === $user;
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        
        if (!($isOwner || $isAdmin)) {
            throw $this->createAccessDeniedException('You do not have permission to delete this journal.');
        }

        if ($this->isCsrfTokenValid('delete'.$creationJournal->getId(), $request->request->get('_token'))) {
            $entityManager->remove($creationJournal);
            $entityManager->flush();
            $this->addFlash('success', 'Le journal a été supprimé.');
        }

        return $this->redirectToRoute('journal_index');
    }

    #[Route('/{id}/add-step', name: 'journal_add_step', methods: ['GET', 'POST'])]
    public function addStep(Request $request, CreationJournal $creationJournal, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!($user && $creationJournal->getArtist() && $creationJournal->getArtist()->getUser() === $user)) {
            throw $this->createAccessDeniedException('You do not have permission to add steps to this journal.');
        }

        $step = new CreationStep();
        $step->setJournal($creationJournal);
        
        $existingSteps = $creationJournal->getSteps();
        $nextStepNumber = count($existingSteps) + 1;
        $step->setStepOrder($nextStepNumber);

        $form = $this->createForm(CreationStepType::class, $step);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($step);
            $entityManager->flush();

            $this->addFlash('success', 'Étape ajoutée avec succès !');
            return $this->redirectToRoute('journal_show', ['id' => $creationJournal->getId()]);
        }

        return $this->render('journal/creation_step/new.html.twig', [
            'journal' => $creationJournal,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/publish', name: 'journal_publish', methods: ['POST'])]
    public function publish(Request $request, CreationJournal $creationJournal, EntityManagerInterface $entityManager, UserRepository $userRepository, EmailService $emailService): Response
    {
        $user = $this->getUser();
        if (!($user && $creationJournal->getArtist() && $creationJournal->getArtist()->getUser() === $user)) {
            throw $this->createAccessDeniedException('You do not have permission to publish this journal.');
        }

        if ($this->isCsrfTokenValid('publish'.$creationJournal->getId(), $request->request->get('_token'))) {
            $wasAlreadyPublished = $creationJournal->getIsPublished();
            $creationJournal->setIsPublished(true);
            $entityManager->flush();
            
            // Send newsletter only if journal wasn't already published
            if (!$wasAlreadyPublished) {
                try {
                    $users = $userRepository->findAll();
                    $this->addFlash('info', 'Utilisateurs trouvés: ' . count($users));
                    
                    $emails = array_filter(array_map(fn($u) => $u->getEmail(), $users));
                    $this->addFlash('info', 'Emails valides: ' . count($emails));
                    
                    if (!empty($emails)) {
                        $emailService->sendNewJournalNotification(
                            $emails,
                            $creationJournal->getTitle(),
                            $creationJournal->getArtist()->getName()
                        );
                        $this->addFlash('success', 'Journal publié et ' . count($emails) . ' notifications envoyées !');
                    } else {
                        $this->addFlash('warning', 'Journal publié mais aucun email valide trouvé !');
                    }
                } catch (\Exception $e) {
                    $this->addFlash('success', 'Journal publié avec succès !');
                    $this->addFlash('error', 'Erreur notifications: ' . $e->getMessage());
                }
            } else {
                $this->addFlash('info', 'Journal déjà publié.');
            }
        }

        return $this->redirectToRoute('journal_show', ['id' => $creationJournal->getId()]);
    }

    #[Route('/{id}/unpublish', name: 'journal_unpublish', methods: ['POST'])]
    public function unpublish(Request $request, CreationJournal $creationJournal, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!($user && $creationJournal->getArtist() && $creationJournal->getArtist()->getUser() === $user)) {
            throw $this->createAccessDeniedException('You do not have permission to unpublish this journal.');
        }

        if ($this->isCsrfTokenValid('unpublish'.$creationJournal->getId(), $request->request->get('_token'))) {
            $creationJournal->setIsPublished(false);
            $entityManager->flush();
            $this->addFlash('success', 'Journal dépublié avec succès.');
        }

        return $this->redirectToRoute('journal_show', ['id' => $creationJournal->getId()]);
    }

    #[Route('/search', name: 'journal_search', methods: ['GET'])]
    public function search(Request $request, CreationJournalRepository $creationJournalRepository): Response
    {
        $query = $request->query->get('q', '');
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = 6;
        $offset = ($page - 1) * $perPage;

        if (empty($query)) {
            return $this->redirectToRoute('journal_index');
        }

        $journals = $creationJournalRepository->searchPublishedJournals($query, $offset, $perPage);
        $total = $creationJournalRepository->countSearchResults($query);
        $totalPages = ceil($total / $perPage);

        return $this->render('journal/creation_journal/search.html.twig', [
            'creation_journals' => $journals,
            'query' => $query,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_items' => $total,
            'per_page' => $perPage,
        ]);
    }

    #[Route('/{id}/pdf', name: 'journal_pdf', methods: ['GET'])]
    public function downloadPdf(CreationJournal $creationJournal, PdfService $pdfService): Response
    {
        if (!$creationJournal->getIsPublished() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        return $pdfService->streamJournalPdf($creationJournal);
    }

    #[Route('/{id}/email-pdf', name: 'journal_email_pdf', methods: ['POST'])]
    public function emailPdf(Request $request, CreationJournal $creationJournal, PdfService $pdfService, EmailService $emailService): Response
    {
        $email = $request->request->get('email');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('error', 'Email invalide.');
            return $this->redirectToRoute('journal_show', ['id' => $creationJournal->getId()]);
        }

        try {
            $pdfPath = $pdfService->generateJournalPdf($creationJournal);
            
            if (!file_exists($pdfPath)) {
                throw new \Exception('PDF non généré: ' . $pdfPath);
            }
            
            $emailService->sendJournalPdf($email, $creationJournal->getTitle(), $pdfPath);
            
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }
            
            $this->addFlash('success', 'PDF envoyé par email avec succès à ' . $email);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur: ' . $e->getMessage());
        }

        return $this->redirectToRoute('journal_show', ['id' => $creationJournal->getId()]);
    }

    #[Route('/{id}/translate', name: 'journal_translate', methods: ['POST'])]
    public function translateJournal(Request $request, CreationJournal $creationJournal, TranslationService $translationService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $targetLang = $data['target'] ?? 'en';

        try {
            if (!$translationService->isConfigured()) {
                return new JsonResponse(['error' => 'Translation service not configured'], 500);
            }

            $translatedData = [
                'title' => $translationService->translateText($creationJournal->getTitle(), $targetLang),
                'description' => $translationService->translateText($creationJournal->getDescription(), $targetLang),
                'steps' => []
            ];

            foreach ($creationJournal->getSteps() as $step) {
                $translatedData['steps'][] = [
                    'id' => $step->getId(),
                    'title' => $translationService->translateText($step->getTitle(), $targetLang),
                    'content' => $translationService->translateText($step->getContent(), $targetLang)
                ];
            }

            return new JsonResponse([
                'success' => true,
                'data' => $translatedData,
                'target_language' => $targetLang
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
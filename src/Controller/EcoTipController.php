<?php

namespace App\Controller;

use App\Entity\EcoTip;
use App\Entity\EcoTipVote;
use App\Form\EcoTipType;
use App\Repository\EcoTipRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/eco-tips')]
class EcoTipController extends AbstractController
{
    #[Route('/', name: 'eco_tip_index', methods: ['GET'])]
    public function index(Request $request, EcoTipRepository $ecoTipRepository): Response
    {
        $search = trim($request->query->get('search', ''));
        $category = $request->query->get('category');
        $sort = $request->query->get('sort', 'recent');
        $page = max(1, (int) $request->query->get('page', 1));
        // Désactiver la pagination côté serveur : récupérer toutes les astuces
        $limit = null;

        $ecoTips = $ecoTipRepository->findApprovedByFilters($category, $sort, $search ?: null, $page, $limit);
        $totalEcoTips = $ecoTipRepository->countApprovedByFilters($category, $search ?: null);
        $totalPages = 1;
        
        $categories = ['Art Recyclé', 'Upcycling', 'Art Naturel', 'Art Écologique', 'Art Durable', 'Art Zéro Déchet'];
        
        return $this->render('eco_tip/index.html.twig', [
            'eco_tips' => $ecoTips,
            'categories' => $categories,
            'current_category' => $category,
            'current_sort' => $sort,
            'current_search' => $search,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_eco_tips' => $totalEcoTips,
        ]);
    }

    #[Route('/new', name: 'eco_tip_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $ecoTip = new EcoTip();
        $ecoTip->setAuthor($this->getUser());
        
        $form = $this->createForm(EcoTipType::class, $ecoTip);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // Handle image upload
                $imageFile = $form->get('image')->getData();
                if ($imageFile) {
                    try {
                        $imageFileName = $fileUploader->upload($imageFile);
                        $ecoTip->setImage([$imageFileName]);
                    } catch (\Exception $e) {
                        $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
                    }
                }
                
                // Auto-approve for now (remove this line if you want manual approval)
                $ecoTip->setApproved(true);
                
                $entityManager->persist($ecoTip);
                $entityManager->flush();

                $this->addFlash('success', 'Votre astuce a été soumise et sera examinée par nos modérateurs.');
                return $this->redirectToRoute('eco_tip_index');
            } else {
                $this->addFlash('error', 'Veuillez corriger les erreurs dans le formulaire.');
            }
        }

        return $this->render('eco_tip/new.html.twig', [
            'eco_tip' => $ecoTip,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/vote', name: 'eco_tip_vote', methods: ['POST'])]
    public function vote(Request $request, EcoTip $ecoTip, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $user = $this->getUser();
        $isUpvote = $request->request->get('upvote') === 'true';
        
        // Check if user already voted
        $existingVote = $entityManager->getRepository(EcoTipVote::class)
            ->findOneBy(['user' => $user, 'ecoTip' => $ecoTip]);
        
        if ($existingVote) {
            if ($existingVote->isUpvote() === $isUpvote) {
                // Remove vote if same
                $entityManager->remove($existingVote);
                $ecoTip->setVotes($ecoTip->getVotes() + ($isUpvote ? -1 : 1));
            } else {
                // Change vote
                $existingVote->setUpvote($isUpvote);
                $ecoTip->setVotes($ecoTip->getVotes() + ($isUpvote ? 2 : -2));
            }
        } else {
            // New vote
            $vote = new EcoTipVote();
            $vote->setUser($user);
            $vote->setEcoTip($ecoTip);
            $vote->setUpvote($isUpvote);
            
            $entityManager->persist($vote);
            $ecoTip->setVotes($ecoTip->getVotes() + ($isUpvote ? 1 : -1));
        }
        
        $entityManager->flush();
        
        return new JsonResponse([
            'success' => true,
            'votes' => $ecoTip->getVotes()
        ]);
    }

    #[Route('/{id}', name: 'eco_tip_show', methods: ['GET'])]
    public function show(EcoTip $ecoTip): Response
    {
        if (!$ecoTip->isApproved()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }
        
        return $this->render('eco_tip/show.html.twig', [
            'eco_tip' => $ecoTip,
        ]);
    }

    #[Route('/{id}/edit', name: 'eco_tip_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EcoTip $ecoTip, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $user = $this->getUser();
        if ($ecoTip->getAuthor() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
        
        $form = $this->createForm(EcoTipType::class, $ecoTip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                try {
                    $imageFileName = $fileUploader->upload($imageFile);
                    $ecoTip->setImage([$imageFileName]);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
                }
            }
            
            $entityManager->flush();

            $this->addFlash('success', 'Astuce mise à jour!');
            return $this->redirectToRoute('eco_tip_index');
        }

        return $this->render('eco_tip/edit.html.twig', [
            'eco_tip' => $ecoTip,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'eco_tip_delete', methods: ['POST'])]
    public function delete(Request $request, EcoTip $ecoTip, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($ecoTip->getAuthor() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
        
        if ($this->isCsrfTokenValid('delete'.$ecoTip->getId(), $request->request->get('_token'))) {
            $entityManager->remove($ecoTip);
            $entityManager->flush();
            $this->addFlash('success', 'Astuce supprimée!');
        }

        return $this->redirectToRoute('eco_tip_index');
    }

    #[Route('/{id}/approve', name: 'eco_tip_approve', methods: ['POST'])]
    public function approve(Request $request, EcoTip $ecoTip, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        if ($this->isCsrfTokenValid('approve'.$ecoTip->getId(), $request->request->get('_token'))) {
            $ecoTip->setApproved(true);
            $entityManager->flush();
            $this->addFlash('success', 'Astuce approuvée!');
        }

        return $this->redirectToRoute('eco_tip_index');
    }
}
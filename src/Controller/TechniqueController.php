<?php

namespace App\Controller;

use App\Entity\Technique;
use App\Form\TechniqueType;
use App\Repository\TechniqueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/techniques')]
class TechniqueController extends AbstractController
{
    #[Route('/', name: 'technique_index', methods: ['GET'])]
    public function index(Request $request, TechniqueRepository $techniqueRepository): Response
    {
        $search = trim($request->query->get('search', ''));
        $category = $request->query->get('category');
        $difficulty = $request->query->get('difficulty');
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = 2;
        
        $techniques = $techniqueRepository->findByFilters($category, $difficulty, $search ?: null, $page, $limit);
        $totalTechniques = $techniqueRepository->countByFilters($category, $difficulty, $search ?: null);
        $totalPages = ceil($totalTechniques / $limit);
        
        // Get actual categories and difficulties from database
        $allTechniques = $techniqueRepository->findAll();
        $categories = array_unique(array_map(fn($t) => $t->getCategory(), $allTechniques));
        $difficulties = array_unique(array_map(fn($t) => $t->getDifficulty(), $allTechniques));
        
        // Fallback to default if no techniques exist
        if (empty($categories)) {
            $categories = ['Recyclage', 'Upcycling', 'Éco-Design', 'Art Naturel', 'Zéro Déchet'];
        }
        if (empty($difficulties)) {
            $difficulties = ['Débutant', 'Intermédiaire', 'Avancé'];
        }
        
        return $this->render('technique/index.html.twig', [
            'techniques' => $techniques,
            'categories' => $categories,
            'difficulties' => $difficulties,
            'current_category' => $category,
            'current_difficulty' => $difficulty,
            'current_search' => $search,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_techniques' => $totalTechniques,
        ]);
    }

    #[Route('/new', name: 'technique_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $technique = new Technique();
        $form = $this->createForm(TechniqueType::class, $technique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFiles = $form->get('images')->getData();
            if ($imageFiles) {
                $imageNames = [];
                foreach ($imageFiles as $imageFile) {
                    $newFilename = uniqid().'.'.$imageFile->guessExtension();
                    $imageFile->move('uploads/', $newFilename);
                    $imageNames[] = $newFilename;
                }
                $technique->setImages($imageNames);
            }
            $entityManager->persist($technique);
            $entityManager->flush();
            return $this->redirectToRoute('technique_index');
        }

        return $this->render('technique/new.html.twig', [
            'technique' => $technique,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'technique_show', methods: ['GET'])]
    public function show(Technique $technique): Response
    {
        return $this->render('technique/show.html.twig', [
            'technique' => $technique,
        ]);
    }

    #[Route('/{id}/edit', name: 'technique_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Technique $technique, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $form = $this->createForm(TechniqueType::class, $technique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFiles = $form->get('images')->getData();
            if ($imageFiles) {
                $imageNames = [];
                foreach ($imageFiles as $imageFile) {
                    $newFilename = uniqid().'.'.$imageFile->guessExtension();
                    $imageFile->move('uploads/', $newFilename);
                    $imageNames[] = $newFilename;
                }
                $technique->setImages($imageNames);
            }
            $entityManager->flush();
            return $this->redirectToRoute('technique_index');
        }

        return $this->render('technique/edit.html.twig', [
            'technique' => $technique,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'technique_delete', methods: ['POST'])]
    public function delete(Request $request, Technique $technique, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        if ($this->isCsrfTokenValid('delete'.$technique->getId(), $request->request->get('_token'))) {
            $entityManager->remove($technique);
            $entityManager->flush();
            $this->addFlash('success', 'Technique supprimée!');
        }

        return $this->redirectToRoute('technique_index');
    }
}
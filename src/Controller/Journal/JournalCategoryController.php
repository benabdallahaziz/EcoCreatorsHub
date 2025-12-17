<?php

namespace App\Controller\Journal;

use App\Entity\JournalCategory;
use App\Repository\JournalCategoryRepository;
use App\Repository\CreationJournalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/journal/category')]
class JournalCategoryController extends AbstractController
{
    #[Route('/', name: 'journal_category_index', methods: ['GET'])]
    public function index(JournalCategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAllOrderedByName();

        return $this->render('journal/category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/{slug}', name: 'journal_category_show', methods: ['GET'])]
    public function show(
        string $slug, 
        Request $request,
        JournalCategoryRepository $categoryRepository,
        CreationJournalRepository $journalRepository
    ): Response {
        $category = $categoryRepository->findOneBy(['slug' => $slug]);
        
        if (!$category) {
            throw $this->createNotFoundException('Catégorie non trouvée.');
        }

        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        // Récupérer seulement les journaux publiés de cette catégorie
        $journals = $journalRepository->findPublishedByCategory($category, $offset, $perPage);
        $total = $journalRepository->countPublishedByCategory($category);
        $totalPages = ceil($total / $perPage);

        return $this->render('journal/category/show.html.twig', [
            'category' => $category,
            'creation_journals' => $journals,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_items' => $total,
            'per_page' => $perPage,
        ]);
    }
}
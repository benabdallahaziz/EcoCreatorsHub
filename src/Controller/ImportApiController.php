<?php

namespace App\Controller;

use App\Service\PdfImportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/import')]
class ImportApiController extends AbstractController
{
    public function __construct(
        private PdfImportService $pdfImportService,
    ) {}

    /**
     * Endpoint pour importer des techniques et astuces depuis un PDF/JSON
     * 
     * POST /api/import/pdf
     * 
     * Paramètres:
     * - file: Le fichier PDF ou JSON à importer
     * 
     * Format JSON attendu:
     * {
     *   "techniques": [
     *     {
     *       "name": "Upcycling de bouteilles",
     *       "description": "Comment transformer les bouteilles en décoration",
     *       "category": "Upcycling",
     *       "difficulty": "Facile",
     *       "materials": "Bouteilles, peinture, colle",
     *       "steps": "1. Nettoyer la bouteille..."
     *     }
     *   ],
     *   "tips": [
     *     {
     *       "title": "Comment recycler le papier",
     *       "content": "Le papier peut être facilement recyclé...",
     *       "category": "Art Recyclé"
     *     }
     *   ]
     * }
     */
    #[Route('/pdf', name: 'api_import_pdf', methods: ['POST'])]
    public function importPdf(Request $request): JsonResponse
    {
        $file = $request->files->get('file');

        if (!$file) {
            return $this->json(
                ['error' => 'Aucun fichier fourni'],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Informations de debug du fichier (utiles si le parsing échoue)
        $fileInfo = [
            'originalName' => $file->getClientOriginalName(),
            'mimeType' => $file->getMimeType(),
            'size' => $file->getSize(),
        ];

        // Valider la taille du fichier (max 10MB)
        if ($fileInfo['size'] > 10 * 1024 * 1024) {
            return $this->json(
                ['error' => 'Le fichier est trop volumineux (max 10MB)', 'file' => $fileInfo],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $result = $this->pdfImportService->importFromPdf($file, $this->getUser());
            // joindre des informations utiles pour le debug côté client
            $result['file'] = $fileInfo;

            return $this->json($result, $result['success'] ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Endpoint pour obtenir un exemple de format JSON à importer
     * 
     * GET /api/import/example
     */
    #[Route('/example', name: 'api_import_example', methods: ['GET'])]
    public function getExample(): JsonResponse
    {
        $example = [
            'techniques' => [
                [
                    'name' => 'Upcycling de bouteilles',
                    'description' => 'Comment transformer les bouteilles en décoration',
                    'category' => 'Upcycling',
                    'difficulty' => 'Facile',
                    'materials' => 'Bouteilles, peinture, colle',
                    'steps' => '1. Nettoyer la bouteille
2. Poncer la surface
3. Appliquer la peinture'
                ],
                [
                    'name' => 'Peinture écologique à base de plantes',
                    'description' => 'Créer une peinture naturelle à partir de plantes',
                    'category' => 'Art Naturel',
                    'difficulty' => 'Moyen',
                    'materials' => 'Plantes, eau, tissu',
                    'steps' => '1. Récolter les plantes
2. Faire bouillir
3. Filtrer'
                ]
            ],
            'tips' => [
                [
                    'title' => 'Comment recycler le papier',
                    'content' => 'Le papier peut être facilement recyclé en créant du papier mâché pour vos projets artistiques.',
                    'category' => 'Art Recyclé',
                    'image' => ['papier-recycle.jpg'] // optionnel
                ],
                [
                    'title' => 'Astuce pour le zéro déchet en art',
                    'content' => 'Utilisez les restes de peinture pour créer de nouveaux mélanges de couleurs.',
                    'category' => 'Art Zéro Déchet'
                ]
            ]
        ];

        return $this->json($example);
    }

    /**
     * Page d'admin pour tester l'import via interface web
     */
    #[Route('/admin/upload', name: 'admin_import_page', methods: ['GET'])]
    public function importPage(): Response
    {
        return $this->render('admin/import.html.twig');
    }
}

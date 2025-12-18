<?php

namespace App\Service;

use App\Entity\EcoTip;
use App\Entity\Technique;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PdfImportService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    /**
     * Importe les données d'un fichier PDF
     * Format attendu : JSON contenant 'techniques' et 'tips' (astuces)
     */
    public function importFromPdf(UploadedFile $file, ?User $author = null): array
    {
        $result = [
            'success' => false,
            'message' => '',
            'techniques_added' => 0,
            'techniques_skipped' => 0,
            'tips_added' => 0,
            'tips_skipped' => 0,
            'errors' => []
        ];

        try {
            // Lire le contenu du fichier
            $filePath = $file->getPathname();
            $fileContent = file_get_contents($filePath);

            // Essayer de parser comme JSON (le PDF peut contenir du JSON)
            $data = json_decode($fileContent, true);

            if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                // Si ce n'est pas du JSON, utiliser un parseur PDF simple
                $data = $this->extractDataFromPdf($filePath);
            }

            if (empty($data)) {
                $result['message'] = 'Impossible de lire les données du fichier PDF';
                return $result;
            }

            // Importer les techniques
            if (!empty($data['techniques'])) {
                foreach ($data['techniques'] as $techniqueData) {
                    try {
                        $this->importTechnique($techniqueData, $result);
                    } catch (\Exception $e) {
                        $result['errors'][] = 'Technique: ' . ($techniqueData['name'] ?? 'Inconnue') . ' - ' . $e->getMessage();
                        $result['techniques_skipped']++;
                    }
                }
            }

            // Importer les astuces
            if (!empty($data['tips']) && $author) {
                foreach ($data['tips'] as $tipData) {
                    try {
                        $this->importEcoTip($tipData, $author, $result);
                    } catch (\Exception $e) {
                        $result['errors'][] = 'Astuce: ' . ($tipData['title'] ?? 'Inconnue') . ' - ' . $e->getMessage();
                        $result['tips_skipped']++;
                    }
                }
            }

            $this->entityManager->flush();
            $result['success'] = true;
            $result['message'] = 'Importation terminée avec succès';

        } catch (\Exception $e) {
            $result['message'] = 'Erreur lors de l\'importation: ' . $e->getMessage();
        }

        return $result;
    }

    /**
     * Importe une technique en vérifiant les doublons
     */
    private function importTechnique(array $techniqueData, array &$result): void
    {
        if (empty($techniqueData['name'])) {
            throw new \Exception('Le nom de la technique est obligatoire');
        }

        // Vérifier si la technique existe déjà
        $existingTechnique = $this->entityManager->getRepository(Technique::class)
            ->findOneBy(['name' => $techniqueData['name']]);

        if ($existingTechnique) {
            $result['techniques_skipped']++;
            return;
        }

        $technique = new Technique();
        $technique->setName($techniqueData['name']);
        $technique->setDescription($techniqueData['description'] ?? '');
        $technique->setCategory($techniqueData['category'] ?? 'Art Écologique');
        $technique->setDifficulty($techniqueData['difficulty'] ?? 'Moyen');

        if (!empty($techniqueData['materials'])) {
            $technique->setMaterials($techniqueData['materials']);
        }

        if (!empty($techniqueData['steps'])) {
            $technique->setSteps($techniqueData['steps']);
        }

        if (!empty($techniqueData['images']) && is_array($techniqueData['images'])) {
            $technique->setImages($techniqueData['images']);
        }

        $this->entityManager->persist($technique);
        $result['techniques_added']++;
    }

    /**
     * Importe une astuce en vérifiant les doublons
     */
    private function importEcoTip(array $tipData, User $author, array &$result): void
    {
        if (empty($tipData['title'])) {
            throw new \Exception('Le titre de l\'astuce est obligatoire');
        }

        // Vérifier si l'astuce existe déjà par titre exact
        $existingTip = $this->entityManager->getRepository(EcoTip::class)
            ->findOneBy(['title' => $tipData['title']]);

        if ($existingTip) {
            $result['tips_skipped']++;
            return;
        }

        // Vérifier par contenu similaire (première 100 caractères)
        if (!empty($tipData['content'])) {
            $contentStart = substr($tipData['content'], 0, 100);
            $qb = $this->entityManager->getRepository(EcoTip::class)
                ->createQueryBuilder('e')
                ->where('e.content LIKE :content')
                ->setParameter('content', $contentStart . '%')
                ->setMaxResults(1);

            if ($qb->getQuery()->getOneOrNullResult()) {
                $result['tips_skipped']++;
                return;
            }
        }

        $tip = new EcoTip();
        $tip->setTitle($tipData['title']);
        $tip->setContent($tipData['content'] ?? '');
        $tip->setCategory($tipData['category'] ?? 'Art Écologique');
        $tip->setAuthor($author);
        $tip->setApproved(true); // Auto-approuver les imports
        $tip->setVotes(0);

        if (!empty($tipData['image']) && is_array($tipData['image'])) {
            $tip->setImage($tipData['image']);
        }

        $this->entityManager->persist($tip);
        $result['tips_added']++;
    }

    /**
     * Extrait les données d'un fichier PDF (simple parsing de texte)
     * Retourne un array avec les données extraites
     */
    private function extractDataFromPdf(string $filePath): ?array
    {
        // Tentative d'extraction du texte si `pdftotext` est disponible
        $text = null;
        if (function_exists('shell_exec')) {
            $cmd = 'pdftotext ' . escapeshellarg($filePath) . ' - 2>/dev/null';
            $text = @shell_exec($cmd);
            if ($text === null || trim($text) === '') {
                $text = null;
            }
        }

        // Si on a du texte, essayer de parser directement comme JSON
        if ($text !== null) {
            $data = json_decode($text, true);
            if ($data !== null && json_last_error() === JSON_ERROR_NONE) {
                return $data;
            }

            // Rechercher un bloc JSON dans le texte
            $start = strpos($text, '{');
            $end = strrpos($text, '}');
            if ($start !== false && $end !== false && $end > $start) {
                $candidate = substr($text, $start, $end - $start + 1);
                $data = json_decode($candidate, true);
                if ($data !== null && json_last_error() === JSON_ERROR_NONE) {
                    return $data;
                }
            }

            // Tentative: parser le texte brut pour en extraire des techniques/astuces
            $parsed = $this->parsePlainText($text);
            if ($parsed !== null) {
                return $parsed;
            }
        }

        // Si pdftotext n'est pas disponible ou n'a rien donné,
        // tenter d'extraire un bloc JSON directement depuis le binaire du PDF
        $raw = @file_get_contents($filePath);
        if ($raw === false) {
            return null;
        }

        $start = strpos($raw, '{');
        $end = strrpos($raw, '}');
        if ($start !== false && $end !== false && $end > $start) {
            $candidate = substr($raw, $start, $end - $start + 1);
            $data = json_decode($candidate, true);
            if ($data !== null && json_last_error() === JSON_ERROR_NONE) {
                return $data;
            }
        }

        // Fallback: parser le contenu brut pour retrouver des sections utiles
        $parsed = $this->parsePlainText($raw);
        if ($parsed !== null) {
            return $parsed;
        }

        // Aucune donnée structurée trouvée
        return null;
    }

    /**
     * Parse un texte brut en cherchant des blocs représentant des techniques ou des astuces.
     * Retourne ['techniques' => [...], 'tips' => [...]] ou null si rien trouvé.
     */
    private function parsePlainText(string $text): ?array
    {
        $blocks = preg_split('/\R\R+/', trim($text));
        $techniques = [];
        $tips = [];

        foreach ($blocks as $block) {
            $lines = preg_split('/\R/', trim($block));
            $kv = [];
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') {
                    continue;
                }
                if (preg_match('/^\s*(Name|Title|Technique)\s*[:\-]\s*(.+)$/i', $line, $m)) {
                    $kv['title'] = trim($m[2]);
                    continue;
                }
                if (preg_match('/^\s*(Description|Desc)\s*[:\-]\s*(.+)$/i', $line, $m)) {
                    $kv['description'] = trim($m[2]);
                    continue;
                }
                if (preg_match('/^\s*(Materials?)\s*[:\-]\s*(.+)$/i', $line, $m)) {
                    $kv['materials'] = trim($m[2]);
                    continue;
                }
                if (preg_match('/^\s*(Steps?)\s*[:\-]\s*(.+)$/i', $line, $m)) {
                    $kv['steps'] = trim($m[2]);
                    continue;
                }
                if (preg_match('/^\s*(Category)\s*[:\-]\s*(.+)$/i', $line, $m)) {
                    $kv['category'] = trim($m[2]);
                    continue;
                }
                if (preg_match('/^\s*(Difficulty)\s*[:\-]\s*(.+)$/i', $line, $m)) {
                    $kv['difficulty'] = trim($m[2]);
                    continue;
                }
                // Accumuler comme corps
                $kv['body'] = isset($kv['body']) ? ($kv['body'] . "\n" . $line) : $line;
            }

            // Déterminer si c'est une technique ou une astuce
            $isTechnique = isset($kv['materials']) || isset($kv['steps']) || isset($kv['difficulty']) || (isset($kv['description']) && strlen($kv['description']) > 50);

            if ($isTechnique) {
                $name = $kv['title'] ?? (strlen($kv['body'] ?? '') > 0 ? substr($kv['body'], 0, 40) : 'Technique inconnue');
                $techniques[] = [
                    'name' => $name,
                    'description' => $kv['description'] ?? ($kv['body'] ?? ''),
                    'category' => $kv['category'] ?? 'Art Écologique',
                    'difficulty' => $kv['difficulty'] ?? 'Moyen',
                    'materials' => $kv['materials'] ?? null,
                    'steps' => $kv['steps'] ?? null,
                ];
            } else {
                $title = $kv['title'] ?? (strlen($kv['body'] ?? '') > 0 ? substr($kv['body'], 0, 40) : 'Astuce');
                $tips[] = [
                    'title' => $title,
                    'content' => $kv['description'] ?? ($kv['body'] ?? ''),
                    'category' => $kv['category'] ?? 'Art Recyclé',
                ];
            }
        }

        if (empty($techniques) && empty($tips)) {
            return null;
        }

        return ['techniques' => $techniques, 'tips' => $tips];
    }
}

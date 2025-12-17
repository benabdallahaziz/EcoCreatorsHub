<?php

namespace App\Controller;

use App\Service\TranslationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TranslationController extends AbstractController
{
    #[Route('/translate', name: 'app_translate', methods: ['POST'])]
    public function translate(Request $request, TranslationService $translationService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $text = $data['text'] ?? '';
        $targetLang = $data['target'] ?? 'en';
        $sourceLang = $data['source'] ?? 'fr';

        if (empty($text)) {
            return new JsonResponse(['error' => 'No text provided'], 400);
        }

        $translatedText = $translationService->translateText($text, $targetLang, $sourceLang);

        return new JsonResponse([
            'original' => $text,
            'translated' => $translatedText,
            'target_language' => $targetLang
        ]);
    }

    #[Route('/languages', name: 'app_languages', methods: ['GET'])]
    public function getSupportedLanguages(TranslationService $translationService): JsonResponse
    {
        return new JsonResponse($translationService->getSupportedLanguages());
    }
}
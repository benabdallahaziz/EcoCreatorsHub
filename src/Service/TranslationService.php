<?php

namespace App\Service;

use Google\Cloud\Translate\V2\TranslateClient;

class TranslationService
{
    private ?TranslateClient $translate = null;
    private string $apiKey;

    public function __construct(string $googleTranslateApiKey)
    {
        $this->apiKey = $googleTranslateApiKey;
        if (!empty($this->apiKey) && $this->apiKey !== 'your_api_key_here') {
            $this->translate = new TranslateClient([
                'key' => $this->apiKey
            ]);
        }
    }

    public function translateText(string $text, string $targetLanguage, string $sourceLanguage = 'fr'): string
    {
        try {
            if ($this->translate === null) {
                throw new \Exception('Google Translate API not configured');
            }

            $result = $this->translate->translate($text, [
                'target' => $targetLanguage,
                'source' => $sourceLanguage
            ]);

            return $result['text'] ?? $text;
        } catch (\Exception $e) {
            throw new \Exception('Translation failed: ' . $e->getMessage());
        }
    }

    public function isConfigured(): bool
    {
        return $this->translate !== null;
    }

    public function getSupportedLanguages(): array
    {
        return [
            'en' => ['name' => 'English', 'flag' => 'us'],
            'es' => ['name' => 'Español', 'flag' => 'es'],
            'de' => ['name' => 'Deutsch', 'flag' => 'de'],
            'it' => ['name' => 'Italiano', 'flag' => 'it'],
            'ar' => ['name' => 'العربية', 'flag' => 'sa'],
            'pt' => ['name' => 'Português', 'flag' => 'pt'],
            'ru' => ['name' => 'Русский', 'flag' => 'ru'],
            'zh' => ['name' => '中文', 'flag' => 'cn'],
            'ja' => ['name' => '日本語', 'flag' => 'jp']
        ];
    }
}
<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ChatbotService
{
    private HttpClientInterface $httpClient;
    private string $apiKey;
    private bool $useAI;

    public function __construct(HttpClientInterface $httpClient, string $huggingfaceApiKey = '', string $openaiApiKey = '', string $openrouterApiKey = '')
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $openrouterApiKey ?: ($openaiApiKey ?: $huggingfaceApiKey);
        $this->useAI = !empty($this->apiKey);
    }

    public function getEcoCreativeResponse(string $message): array
    {
        if (strpos($this->apiKey, 'sk-or-') === 0) {
            return $this->getOpenRouterResponse($message);
        }
        if (strpos($this->apiKey, 'sk-') === 0) {
            return $this->getOpenAIResponse($message);
        }
        return $this->getDeepSeekResponse($message);
    }

    private function getOpenRouterResponse(string $message): array
    {
        try {
            $response = $this->httpClient->request('POST', 'https://openrouter.ai/api/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'mistralai/devstral-2512:free',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Tu es un assistant éco-créatif expert en recyclage, upcycling et art durable. Réponds en français de manière concise (max 80 mots) avec des conseils pratiques. Sois inspirant et positif! 🌿'
                        ],
                        [
                            'role' => 'user',
                            'content' => $message
                        ]
                    ],
                    'max_tokens' => 100,
                    'temperature' => 0.7
                ],
                'timeout' => 15
            ]);

            $data = $response->toArray();
            
            if (isset($data['choices'][0]['message']['content'])) {
                $aiResponse = trim($data['choices'][0]['message']['content']);
                
                return [
                    'success' => true,
                    'response' => $aiResponse . ' ✨',
                    'suggestions' => $this->getSmartSuggestions($message)
                ];
            }
        } catch (\Exception $e) {
            // Fallback to local responses
        }
        
        return $this->getDeepSeekResponse($message);
    }

    private function getOpenAIResponse(string $message): array
    {
        try {
            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Tu es un assistant éco-créatif expert en recyclage, upcycling et art durable. Réponds en français de manière concise (max 80 mots) avec des conseils pratiques. Sois inspirant et positif! 🌿'
                        ],
                        [
                            'role' => 'user',
                            'content' => $message
                        ]
                    ],
                    'max_tokens' => 100,
                    'temperature' => 0.7
                ],
                'timeout' => 10
            ]);

            $data = $response->toArray();
            
            if (isset($data['choices'][0]['message']['content'])) {
                $aiResponse = trim($data['choices'][0]['message']['content']);
                
                return [
                    'success' => true,
                    'response' => '🤖 OpenAI: ' . $aiResponse . ' ✨',
                    'suggestions' => $this->getSmartSuggestions($message)
                ];
            }
        } catch (\Exception $e) {
            // Fallback to enhanced local AI
        }
        
        return $this->getDeepSeekResponse($message);
    }

    private function getDeepSeekResponse(string $message): array
    {
        // Simple AI simulation with smart responses
        $ecoResponses = [
            'recyclage' => 'Excellente idée! Pour le recyclage créatif, commencez avec du carton et des bouteilles plastique. Créez des sculptures, pots de fleurs ou organisateurs. L’art du recyclage transforme les déchets en trésors! 🌱',
            'upcycling' => 'L’upcycling est fantastique! Transformez vieux meubles avec peinture écologique, créez des étagères avec caisses en bois, ou des sacs avec vieux vêtements. Chaque objet peut avoir une seconde vie créative! ✨',
            'matériaux' => 'Matériaux éco-friendly: bois récupéré, carton, tissu usagé, bouteilles, journaux, liège. Privilégiez les colles naturelles et peintures à l’eau. La nature offre aussi: feuilles, branches, pierres! 🌿',
            'inspiration' => 'Pour l’inspiration: observez la nature (formes, textures, couleurs), visitez nos journaux d’artistes, explorez Pinterest éco-art. Les couleurs terre et formes organiques sont tendance! 🎨',
            'débutant' => 'Débutants: commencez simple! Collages avec magazines, sculptures en carton, peinture sur galets. Outils de base: ciseaux, colle naturelle, pinceaux. L’important est de créer avec plaisir! 😊'
        ];

        $message = strtolower($message);
        
        foreach ($ecoResponses as $keyword => $response) {
            if (strpos($message, $keyword) !== false) {
                return [
                    'success' => true,
                    'response' => $response,
                    'suggestions' => $this->getSmartSuggestions($keyword)
                ];
            }
        }

        // General creative response
        return [
            'success' => true,
            'response' => '🤖 Éco-Assistant: Votre projet m’intéresse! Je peux vous conseiller sur les matériaux durables, techniques créatives et sources d’inspiration. Quel aspect vous intéresse le plus? 🌱✨',
            'suggestions' => ['Matériaux écologiques', 'Techniques upcycling', 'Inspiration créative']
        ];
    }



    private function getSmartSuggestions(string $context): array
    {
        $suggestions = [
            'recyclage' => ['Upcycling facile', 'Matériaux recyclés', 'Tutoriels créatifs'],
            'matériaux' => ['Bois récupéré', 'Carton créatif', 'Tissu upcyclé'],
            'inspiration' => ['Art naturel', 'Couleurs terre', 'Formes organiques']
        ];

        return $suggestions[strtolower($context)] ?? ['Créer un journal', 'Explorer matériaux', 'Voir tutoriels'];
    }
}
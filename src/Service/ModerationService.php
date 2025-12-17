<?php

namespace App\Service;

use Twilio\Rest\Client;

class ModerationService
{
    private array $badWords = ['kalma khayba'];
    private ?Client $twilio = null;
    private string $adminPhone;

    public function __construct(
        string $twilioSid,
        string $twilioToken, 
        string $twilioPhone,
        string $adminPhone
    ) {
        $this->adminPhone = $adminPhone;
        
        if ($twilioSid !== 'your_twilio_sid') {
            $this->twilio = new Client($twilioSid, $twilioToken);
            $this->twilioPhone = $twilioPhone;
        }
    }

    public function checkContent(string $content, string $artistName, string $journalTitle): bool
    {
        foreach ($this->badWords as $badWord) {
            if (stripos($content, $badWord) !== false) {
                $this->sendAlert($artistName, $journalTitle, $badWord);
                return true;
            }
        }
        return false;
    }

    private function sendAlert(string $artistName, string $journalTitle, string $badWord): void
    {
        if (!$this->twilio) {
            error_log('Twilio not configured - SID or Token missing');
            return;
        }

        try {
            $message = "🚨 ALERTE MODÉRATION\n\n";
            $message .= "Artiste: {$artistName}\n";
            $message .= "Journal: {$journalTitle}\n";
            $message .= "Mot détecté: {$badWord}\n\n";
            $message .= "Vérifiez le contenu sur EcoCreatorsHub";

            $this->twilio->messages->create(
                $this->adminPhone,
                [
                    'from' => $this->twilioPhone,
                    'body' => $message
                ]
            );
        } catch (\Exception $e) {
            error_log('Twilio SMS failed: ' . $e->getMessage());
            error_log('Error details: ' . $e->getTraceAsString());
        }
    }
}
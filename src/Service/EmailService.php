<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class EmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig
    ) {}

    public function sendJournalPdf(string $to, string $journalTitle, string $pdfPath): void
    {
        if (!file_exists($pdfPath)) {
            throw new \Exception('PDF file not found: ' . $pdfPath);
        }
        
        $email = (new Email())
            ->from('fadisaidi02@gmail.com')
            ->to($to)
            ->subject('Votre journal PDF - ' . $journalTitle)
            ->html($this->twig->render('emails/journal_pdf.html.twig', [
                'journal_title' => $journalTitle
            ]))
            ->attachFromPath($pdfPath);

        $this->mailer->send($email);
    }

    public function sendNewJournalNotification(array $emails, string $journalTitle, string $artistName): void
    {
        if (empty($emails)) {
            throw new \Exception('Aucun email à notifier');
        }
        
        foreach ($emails as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }
            
            $emailMessage = (new Email())
                ->from('fadisaidi02@gmail.com')
                ->to($email)
                ->subject('Nouveau journal publié - ' . $journalTitle)
                ->html($this->twig->render('emails/new_journal.html.twig', [
                    'journal_title' => $journalTitle,
                    'artist_name' => $artistName
                ]));

            $this->mailer->send($emailMessage);
        }
    }
}
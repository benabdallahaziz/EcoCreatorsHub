<?php

namespace App\Service;

use App\Entity\CreationJournal;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Twig\Environment;

class PdfService
{
    public function __construct(
        private Pdf $pdf,
        private Environment $twig
    ) {}

    public function generateJournalPdf(CreationJournal $journal): string
    {
        $html = $this->twig->render('pdf/journal.html.twig', [
            'journal' => $journal,
            'steps' => $journal->getSteps()
        ]);

        $filename = sprintf('journal_%s_%s.pdf', 
            $journal->getId(), 
            date('Y-m-d')
        );

        $pdfPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;
        
        $this->pdf->generateFromHtml($html, $pdfPath, [
            'page-size' => 'A4',
            'encoding' => 'utf-8',
            'images' => true,
            'enable-local-file-access' => true
        ]);

        return $pdfPath;
    }

    public function streamJournalPdf(CreationJournal $journal): PdfResponse
    {
        $html = $this->twig->render('pdf/journal.html.twig', [
            'journal' => $journal,
            'steps' => $journal->getSteps()
        ]);

        $filename = sprintf('journal_%s.pdf', $journal->getTitle());

        return new PdfResponse(
            $this->pdf->getOutputFromHtml($html),
            $filename
        );
    }
}
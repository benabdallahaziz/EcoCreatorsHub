<?php

namespace App\Controller\Journal;

use App\Entity\CreationStep;
use App\Form\CreationStepType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/journal/step')]
class CreationStepController extends AbstractController
{
    #[Route('/{id}', name: 'step_show', methods: ['GET'])]
    public function show(CreationStep $step): Response
    {
        $journal = $step->getJournal();
        
        // Vérifier les permissions
        if (!$journal->getIsPublished()) {
            $user = $this->getUser();
            $artist = $journal->getArtist();
            if (!($user && $artist && $artist->getUser() === $user)) {
                throw $this->createAccessDeniedException('Cette étape n\'est pas accessible.');
            }
        }

        return $this->render('journal/creation_step/show.html.twig', [
            'step' => $step,
            'journal' => $journal,
        ]);
    }

    #[Route('/{id}/edit', name: 'step_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CreationStep $step, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $journal = $step->getJournal();
        $user = $this->getUser();
        if (!($user && $journal->getArtist() && $journal->getArtist()->getUser() === $user)) {
            throw $this->createAccessDeniedException('You do not have permission to edit this step.');
        }

        $form = $this->createForm(CreationStepType::class, $step);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle uploaded images
            $uploadedFiles = $form->get('images')->getData();
            $images = $step->getImages() ?? [];
            if ($uploadedFiles) {
                foreach ($uploadedFiles as $file) {
                    if ($file instanceof UploadedFile) {
                        $images[] = $fileUploader->upload($file);
                    }
                }
            }
            $step->setImages($images);

            $entityManager->flush();

            $this->addFlash('success', 'Étape mise à jour avec succès !');
            return $this->redirectToRoute('journal_show', ['id' => $journal->getId()]);
        }

        return $this->render('journal/creation_step/edit.html.twig', [
            'step' => $step,
            'journal' => $journal,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'step_delete', methods: ['POST'])]
    public function delete(Request $request, CreationStep $step, EntityManagerInterface $entityManager): Response
    {
        $journal = $step->getJournal();
        $user = $this->getUser();
        if (!($user && $journal->getArtist() && $journal->getArtist()->getUser() === $user)) {
            throw $this->createAccessDeniedException('You do not have permission to delete this step.');
        }

        if ($this->isCsrfTokenValid('delete'.$step->getId(), $request->request->get('_token'))) {
            $entityManager->remove($step);
            $entityManager->flush();
            $this->addFlash('success', 'Étape supprimée avec succès.');
        }

        return $this->redirectToRoute('journal_show', ['id' => $journal->getId()]);
    }
}
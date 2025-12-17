<?php

namespace App\Controller;

use App\Entity\CreationJournal;
use App\Entity\JournalLike;
use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/journal')]
class JournalInteractionController extends AbstractController
{
    #[Route('/{id}/like', name: 'journal_like', methods: ['POST'])]
    public function toggleLike(CreationJournal $journal, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $user = $this->getUser();
        $existingLike = $em->getRepository(JournalLike::class)
            ->findOneBy(['user' => $user, 'creationJournal' => $journal]);

        if ($existingLike) {
            $em->remove($existingLike);
            $liked = false;
        } else {
            $like = new JournalLike();
            $like->setUser($user);
            $like->setCreationJournal($journal);
            $em->persist($like);
            $liked = true;
        }

        $em->flush();

        $totalLikes = $em->getRepository(JournalLike::class)
            ->count(['creationJournal' => $journal]);

        return new JsonResponse([
            'liked' => $liked,
            'total_likes' => $totalLikes
        ]);
    }

    #[Route('/{id}/comment', name: 'journal_comment', methods: ['POST'])]
    public function addComment(CreationJournal $journal, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $content = $request->request->get('content');
        if (empty(trim($content))) {
            return new JsonResponse(['error' => 'Le commentaire ne peut pas être vide'], 400);
        }

        $comment = new Comment();
        $comment->setUser($this->getUser());
        $comment->setCreationJournal($journal);
        $comment->setContent($content);

        $em->persist($comment);
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'comment' => [
                'id' => $comment->getId(),
                'content' => $comment->getContent(),
                'author' => $comment->getUser()->getUsername(),
                'created_at' => $comment->getCreatedAt()->format('d/m/Y H:i')
            ]
        ]);
    }
}
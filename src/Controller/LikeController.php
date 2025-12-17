<?php

namespace App\Controller;

use App\Entity\CreationJournal;
use App\Entity\JournalLike;
use App\Entity\JournalView;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class LikeController extends AbstractController
{
    #[Route('/journal/{id}/like', name: 'journal_like', methods: ['POST'])]
    public function toggleLike(CreationJournal $journal, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Not authenticated'], 401);
        }

        $existingLike = $em->getRepository(JournalLike::class)
            ->findOneBy(['user' => $user, 'creationJournal' => $journal]);

        if ($existingLike) {
            $em->remove($existingLike);
            $liked = false;
        } else {
            $like = new JournalLike();
            $like->setUser($user)->setCreationJournal($journal);
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

    #[Route('/journal/{id}/view', name: 'journal_view', methods: ['POST'])]
    public function recordView(CreationJournal $journal, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['success' => false]);
        }

        $existingView = $em->getRepository(JournalView::class)
            ->findOneBy(['user' => $user, 'journal' => $journal]);

        if (!$existingView) {
            $view = new JournalView();
            $view->setUser($user)->setJournal($journal);
            $em->persist($view);
            $em->flush();
        }

        $totalViews = $em->getRepository(JournalView::class)
            ->count(['journal' => $journal]);

        return new JsonResponse([
            'success' => true,
            'total_views' => $totalViews
        ]);
    }
}
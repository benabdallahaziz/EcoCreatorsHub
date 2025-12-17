<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Artist;
use App\Entity\CreationJournal;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function dashboard(EntityManagerInterface $em): Response
    {
        $stats = [
            'users' => $em->getRepository(User::class)->count([]),
            'artists' => $em->getRepository(Artist::class)->count([]),
            'journals' => $em->getRepository(CreationJournal::class)->count([]),
            'published_journals' => $em->getRepository(CreationJournal::class)->count(['isPublished' => true]),
        ];

        $recentUsers = $em->getRepository(User::class)->findBy([], ['createdAt' => 'DESC'], 10);
        $recentJournals = $em->getRepository(CreationJournal::class)->findBy([], ['date' => 'DESC'], 10);

        return $this->render('admin/dashboard.html.twig', [
            'stats' => $stats,
            'recent_users' => $recentUsers,
            'recent_journals' => $recentJournals,
        ]);
    }
}
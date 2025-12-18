<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Artist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/admin/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_admin_user_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $search = $request->query->get('search', '');
        $role = $request->query->get('role', '');
        $status = $request->query->get('status', '');
        $page = max(1, (int)$request->query->get('page', 1));
        $limit = 20;

        $userRepo = $entityManager->getRepository(User::class);
        $qb = $userRepo->createQueryBuilder('u');

        // Filtres
        if ($search) {
            $qb->andWhere('u.username LIKE :search OR u.email LIKE :search')
               ->setParameter('search', '%'.$search.'%');
        }

        if ($role) {
            $qb->andWhere(':role MEMBER OF u.roles')
               ->setParameter('role', $role);
        }

        if ($status) {
            switch ($status) {
                case 'active': $qb->andWhere('u.isActive = true'); break;
                case 'inactive': $qb->andWhere('u.isActive = false'); break;
                case 'verified': $qb->andWhere('u.isVerified = true'); break;
                case 'unverified': $qb->andWhere('u.isVerified = false'); break;
                case 'banned':
                    $qb->andWhere(':banned MEMBER OF u.roles')
                       ->setParameter('banned', 'ROLE_BANNED');
                    break;
            }
        }

        $totalUsers = count($qb->getQuery()->getResult());
        $totalPages = (int) ceil($totalUsers / $limit);

        $users = $qb->setFirstResult(($page - 1) * $limit)
                    ->setMaxResults($limit)
                    ->getQuery()
                    ->getResult();

        $activeUsers = $userRepo->count(['isActive' => true]);
        $verifiedUsers = $userRepo->count(['isVerified' => true]);

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
            'search' => $search,
            'role' => $role,
            'status' => $status,
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'verified_users' => $verifiedUsers,
            'current_page' => $page,
            'total_pages' => $totalPages,
        ]);
    }

    #[Route('/new', name: 'app_admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            $token = $request->request->get('_token');
            if (!$this->isCsrfTokenValid('user_create', $token)) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('app_admin_user_new');
            }

            $username = trim($request->request->get('username'));
            $email = strtolower(trim($request->request->get('email')));
            $password = $request->request->get('password');
            $confirmPassword = $request->request->get('confirm_password');
            $role = $request->request->get('role', 'ROLE_USER');
            $isActive = $request->request->get('isActive', 1);

            // Validation simple
            if ($password !== $confirmPassword) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->redirectToRoute('app_admin_user_new');
            }
            if (strlen($username) < 3 || !preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
                $this->addFlash('error', 'Nom d\'utilisateur invalide.');
                return $this->redirectToRoute('app_admin_user_new');
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('error', 'Email invalide.');
                return $this->redirectToRoute('app_admin_user_new');
            }
            if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
                $this->addFlash('error', 'Mot de passe invalide. Minimum 8 caractères avec lettres et chiffres.');
                return $this->redirectToRoute('app_admin_user_new');
            }

            try {
                $user = new User();
                $user->setUsername($username)
                     ->setEmail($email)
                     ->setPassword($passwordHasher->hashPassword($user, $password))
                     ->setRoles([$role])
                     ->setIsActive((bool)$isActive);

                $entityManager->persist($user);
                $entityManager->flush();

                // Création du profil artiste si rôle ROLE_ARTIST
                if ($user->hasRole('ROLE_ARTIST')) {
                    $artistRepo = $entityManager->getRepository(Artist::class);
                    $existingArtist = $artistRepo->findOneBy(['user' => $user]);
                    if (!$existingArtist) {
                        $artist = new Artist();
                        $artist->setUser($user)
                               ->setName($user->getUsername())
                               ->setBio('')
                               ->setEcoTechnique('');
                        $entityManager->persist($artist);
                        $entityManager->flush();
                    }
                }

                $this->addFlash('success', 'L\'utilisateur a été créé avec succès.');
                return $this->redirectToRoute('app_admin_user_index');

            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la création de l\'utilisateur : '.$e->getMessage());
                return $this->redirectToRoute('app_admin_user_new');
            }
        }

        return $this->render('admin/user/new.html.twig', []);
    }

   #[Route('/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
   public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
   {
       $this->denyAccessUnlessGranted('ROLE_ADMIN');

       if ($request->isMethod('POST')) {
           $token = $request->request->get('_token');
           if (!$this->isCsrfTokenValid('user_create', $token)) {
               $this->addFlash('error', 'Token CSRF invalide.');
               return $this->redirectToRoute('app_admin_user_edit', ['id' => $user->getId()]);
           }

           $data = $request->request->all('user');
           if (!is_array($data)) {
               $data = [];
           }

           $username = trim($data['username'] ?? '');
           $email = strtolower(trim($data['email'] ?? ''));
           $plainPassword = $data['plainPassword'] ?? '';
           $confirmPassword = $data['confirmPassword'] ?? '';
           $roles = $data['roles'] ?? ['ROLE_USER'];
           $isVerified = isset($data['isVerified']);
           $isActive = isset($data['isActive']);

           // Validation
           if ($plainPassword && $plainPassword !== $confirmPassword) {
               $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
               return $this->redirectToRoute('app_admin_user_edit', ['id' => $user->getId()]);
           }

           if (strlen($username) < 3 || !preg_match('/^[a-zA-Z0-9_\-]+$/', $username)) {
               $this->addFlash('error', 'Nom d\'utilisateur invalide.');
               return $this->redirectToRoute('app_admin_user_edit', ['id' => $user->getId()]);
           }

           try {
               $user->setUsername($username);
               $user->setEmail($email);

               if ($plainPassword) {
                   $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
               }

               // Ajouter ROLE_USER si absent
               if (!in_array('ROLE_USER', $roles)) {
                   $roles[] = 'ROLE_USER';
               }
               $user->setRoles(array_unique($roles));
               $user->setIsVerified($isVerified);
               $user->setIsActive($isActive);

               $entityManager->flush();

               // Gestion du profil artiste
               $artistRepo = $entityManager->getRepository(Artist::class);
               $existingArtist = $artistRepo->findOneBy(['user' => $user]);

               if ($user->hasRole('ROLE_ARTIST')) {
                   // Créer ou mettre à jour le profil artiste
                   if (!$existingArtist) {
                       $artist = new Artist();
                       $artist->setUser($user)
                              ->setName($user->getUsername())
                              ->setBio('')
                              ->setEcoTechnique('');
                       $entityManager->persist($artist);
                       $entityManager->flush();
                   } else {
                       // Mettre à jour le nom si username a changé
                       $existingArtist->setName($user->getUsername());
                       $entityManager->flush();
                   }
               } else {
                   // Supprimer le profil artiste si le rôle est retiré
                   if ($existingArtist) {
                       $entityManager->remove($existingArtist);
                       $entityManager->flush();
                   }
               }

               $this->addFlash('success', 'L\'utilisateur a été modifié avec succès.');
               return $this->redirectToRoute('app_admin_user_index');

           } catch (\Exception $e) {
               $this->addFlash('error', 'Une erreur est survenue : ' . $e->getMessage());
           }
       }

       return $this->render('admin/user/edit.html.twig', [
           'user' => $user,
       ]);
   }

    #[Route('/{id}/delete', name: 'app_admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $this->checkUserAccess($user);

        // Vérifier si l'admin essaie de supprimer son propre compte
        $currentUser = $this->getUser();
        if ($currentUser && $currentUser->getId() === $user->getId()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            return $this->redirectToRoute('app_admin_user_index');
        }

        // Vérifier si c'est le dernier admin
        if ($user->hasRole('ROLE_ADMIN')) {
            $adminCount = $entityManager->getRepository(User::class)
                ->createQueryBuilder('u')
                ->select('COUNT(u.id)')
                ->where('u.roles LIKE :role')
                ->setParameter('role', '%ROLE_ADMIN%')
                ->getQuery()
                ->getSingleScalarResult();

            if ($adminCount <= 1) {
                $this->addFlash('error', 'Vous ne pouvez pas supprimer le dernier administrateur du système.');
                return $this->redirectToRoute('app_admin_user_index');
            }
        }

        if (!$this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_admin_user_index');
        }

        try {
            // Supprimer le profil artiste associé s'il existe
            $artistRepo = $entityManager->getRepository(Artist::class);
            $artist = $artistRepo->findOneBy(['user' => $user]);
            if ($artist) {
                $entityManager->remove($artist);
            }

            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur et son profil artiste ont été supprimés avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression : '.$e->getMessage());
        }

        return $this->redirectToRoute('app_admin_user_index');
    }

    private function checkUserAccess(User $user): void
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Vous n\'avez pas les droits nécessaires.');
        }
    }
#[Route('/{id}', name: 'app_admin_user_show', methods: ['GET'])]
public function show(User $user): Response
{
    $this->denyAccessUnlessGranted('ROLE_ADMIN');
    $this->checkUserAccess($user);

    return $this->render('admin/user/show.html.twig', [
        'user' => $user,
    ]);
}


    // … Les méthodes activate, deactivate, verify restent inchangées



        #[Route('/{id}/activate', name: 'app_admin_user_activate', methods: ['POST'])]
        public function activate(Request $request, User $user, EntityManagerInterface $entityManager): Response
        {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');

            if ($this->isCsrfTokenValid('activate'.$user->getId(), $request->request->get('_token'))) {
                $user->activate();
                $entityManager->flush();
                $this->addFlash('success', 'L\'utilisateur a été activé avec succès.');
            } else {
                $this->addFlash('error', 'Token CSRF invalide.');
            }

            return $this->redirectToRoute('app_admin_user_index');
        }

        #[Route('/{id}/deactivate', name: 'app_admin_user_deactivate', methods: ['POST'])]
        public function deactivate(Request $request, User $user, EntityManagerInterface $entityManager): Response
        {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');

            $currentUser = $this->getUser();
            if ($currentUser && $currentUser->getId() === $user->getId()) {
                $this->addFlash('error', 'Vous ne pouvez pas désactiver votre propre compte.');
                return $this->redirectToRoute('app_admin_user_index');
            }

            if ($this->isCsrfTokenValid('deactivate'.$user->getId(), $request->request->get('_token'))) {
                $user->deactivate();
                $entityManager->flush();
                $this->addFlash('success', 'L\'utilisateur a été désactivé avec succès.');
            } else {
                $this->addFlash('error', 'Token CSRF invalide.');
            }

            return $this->redirectToRoute('app_admin_user_index');
        }

        #[Route('/{id}/verify', name: 'app_admin_user_verify', methods: ['POST'])]
        public function verify(Request $request, User $user, EntityManagerInterface $entityManager): Response
        {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');

            if ($this->isCsrfTokenValid('verify'.$user->getId(), $request->request->get('_token'))) {
                $user->setIsVerified(true);
                $entityManager->flush();
                $this->addFlash('success', 'L\'utilisateur a été vérifié avec succès.');
            } else {
                $this->addFlash('error', 'Token CSRF invalide.');
            }

            return $this->redirectToRoute('app_admin_user_index');
        }
    }

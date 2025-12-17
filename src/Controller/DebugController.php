<?php
// src/Controller/DebugController.php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class DebugController extends AbstractController
{
    #[Route('/debug/check-admin', name: 'app_debug_check_admin')]
    public function checkAdmin(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $email = 'admin@test.com';
        $password = 'admin123';

        // Chercher l'utilisateur
        $user = $entityManager->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        if (!$user) {
            return new Response('❌ Utilisateur non trouvé: ' . $email);
        }

        // Afficher toutes les infos
        $info = [
            'ID' => $user->getId(),
            'Email' => $user->getEmail(),
            'Username' => $user->getUsername(),
            'Roles' => $user->getRoles(),
            'isVerified' => $user->isVerified(),
            'isActive' => $user->isActive(),
            'Created At' => $user->getCreatedAt() ? $user->getCreatedAt()->format('Y-m-d H:i:s') : 'NULL',
            'Password Hash' => $user->getPassword(),
            'Password Check' => $passwordHasher->isPasswordValid($user, $password) ? '✅ VALIDE' : '❌ INVALIDE',
            'User Class' => get_class($user),
        ];

        // Tester le hash
        $testHash = $passwordHasher->hashPassword($user, $password);

        $html = '<!DOCTYPE html>
        <html>
        <head><title>Debug Admin</title></head>
        <body style="font-family: Arial; padding: 20px;">
            <h1>🔍 Debug Admin Check</h1>

            <h2>Informations de l\'utilisateur admin@test.com</h2>
            <table border="1" cellpadding="10" style="border-collapse: collapse;">
        ';

        foreach ($info as $key => $value) {
            if ($key === 'Password Hash') {
                $html .= '<tr><th>' . $key . '</th><td><code style="word-break: break-all;">' . htmlspecialchars($value) . '</code></td></tr>';
            } elseif ($key === 'Roles') {
                $html .= '<tr><th>' . $key . '</th><td>' . implode(', ', $value) . '</td></tr>';
            } else {
                $html .= '<tr><th>' . $key . '</th><td>' . htmlspecialchars((string)$value) . '</td></tr>';
            }
        }

        $html .= '</table>

            <h2>Hashes de comparaison</h2>
            <p><strong>Hash actuel:</strong><br><code style="word-break: break-all;">' . htmlspecialchars($info['Password Hash']) . '</code></p>
            <p><strong>Hash attendu pour "admin123":</strong><br><code style="word-break: break-all;">$2y$13$wDzT3qN7N4G5H8J9K0L1M2N3O4P5Q6R7S8T9U0V1W2X3Y4Z5A6B7C8D9</code></p>
            <p><strong>Nouveau hash généré:</strong><br><code style="word-break: break-all;">' . htmlspecialchars($testHash) . '</code></p>

            <p><a href="/debug/fix-admin" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">🔧 Corriger l\'admin automatiquement</a></p>
            <p><a href="/login" style="color: #007bff;">🔐 Aller à la page de login</a></p>
            <p><a href="/admin/user/" style="color: #007bff;">🚀 Tester l\'accès admin directement</a></p>
        </body>
        </html>';

        return new Response($html);
    }

    #[Route('/debug/fix-admin', name: 'app_debug_fix_admin')]
    public function fixAdmin(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $email = 'admin@test.com';
        $password = 'admin123';

        // Chercher ou créer l'admin
        $user = $entityManager->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        if (!$user) {
            $user = new User();
            $user->setEmail($email);
            $user->setUsername('admin');
            $entityManager->persist($user);
        }

        // Mettre à jour les propriétés
        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $user->setIsVerified(true);
        $user->setIsActive(true);
        $user->setCreatedAt(new \DateTime());

        // Hacher le mot de passe
        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $entityManager->flush();

        $html = '<!DOCTYPE html>
        <html>
        <head><title>Admin Fixed</title></head>
        <body style="font-family: Arial; padding: 20px;">
            <h1>✅ Admin corrigé!</h1>

            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin: 20px 0;">
                <strong>Informations mises à jour:</strong><br>
                Email: ' . $email . '<br>
                Mot de passe: ' . $password . '<br>
                Hash: ' . htmlspecialchars($hashedPassword) . '<br>
                Rôles: ROLE_ADMIN, ROLE_USER<br>
                Vérifié: Oui<br>
                Actif: Oui
            </div>

            <p><a href="/debug/check-admin" style="color: #007bff;">🔍 Vérifier l\'admin</a></p>
            <p><a href="/login" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block;">🔐 Tester le login maintenant</a></p>
            <p><a href="/admin/user/" style="color: #007bff;">🚀 Tester l\'accès admin directement</a></p>
        </body>
        </html>';

        return new Response($html);
    }

    #[Route('/debug/test-login', name: 'app_debug_test_login')]
    public function testLogin(): Response
    {
        $html = '<!DOCTYPE html>
        <html>
        <head><title>Test Login</title></head>
        <body style="font-family: Arial; padding: 20px;">
            <h1>🧪 Test de Login</h1>

            <form method="post" action="/login" style="max-width: 400px;">
                <input type="hidden" name="_csrf_token" value="test_token_123">

                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px;"><strong>Email:</strong></label>
                    <input type="email" name="email" value="admin@test.com" required
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px;"><strong>Mot de passe:</strong></label>
                    <input type="password" name="password" value="admin123" required
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>

                <button type="submit" style="background: #007bff; color: white; border: none; padding: 12px 24px; border-radius: 4px; cursor: pointer;">
                    Tester la connexion
                </button>
            </form>

            <div style="margin-top: 30px; background: #f8f9fa; padding: 15px; border-radius: 4px;">
                <h3>🔑 Identifiants de test:</h3>
                <ul>
                    <li><strong>Email:</strong> admin@test.com</li>
                    <li><strong>Mot de passe:</strong> admin123</li>
                    <li><strong>ID attendu:</strong> 8</li>
                </ul>
            </div>
        </body>
        </html>';

        return new Response($html);
    }
}
<?php

namespace App\Controller\Dev;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DebugController extends AbstractController
{
    #[Route('/_debug/users', name: 'debug_users')]
    public function users(UserRepository $repo): JsonResponse
    {
        $users = $repo->findAll();
        $data = array_map(function($u) {
            return [
                'id' => $u->getId(),
                'email' => $u->getEmail(),
                'username' => $u->getUsername(),
                'roles' => $u->getRoles(),
            ];
        }, $users);

        return new JsonResponse($data);
    }

    #[Route('/_debug/check-password', name: 'debug_check_password')]
    public function checkPassword(Request $request, UserRepository $repo, UserPasswordHasherInterface $hasher): JsonResponse
    {
        $email = $request->query->get('email');
        $plain = $request->query->get('plain');

        if (!$email || !$plain) {
            return new JsonResponse(['error' => 'Provide email and plain query params'], 400);
        }

        $user = $repo->findOneBy(['email' => $email]);
        if (!$user) {
            return new JsonResponse(['ok' => false, 'reason' => 'user_not_found']);
        }

        $valid = $hasher->isPasswordValid($user, $plain);

        return new JsonResponse(['ok' => $valid, 'email' => $email]);
    }

    #[Route('/_debug/user-hash', name: 'debug_user_hash')]
    public function userHash(Request $request, UserRepository $repo): JsonResponse
    {
        $email = $request->query->get('email');
        if (!$email) {
            return new JsonResponse(['error' => 'Provide email query param'], 400);
        }

        $user = $repo->findOneBy(['email' => $email]);
        if (!$user) {
            return new JsonResponse(['ok' => false, 'reason' => 'user_not_found']);
        }

        // WARNING: exposing password hashes is insecure; this endpoint is for dev debugging only.
        return new JsonResponse(['ok' => true, 'email' => $email, 'hash' => $user->getPassword()]);
    }

    #[Route('/_debug/login-test', name: 'debug_login_test', methods: ['POST'])]
    public function loginTest(Request $request, UserRepository $repo, UserPasswordHasherInterface $hasher): JsonResponse
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        $debug = [
            'received_email' => $email,
            'received_password' => $password,
            'email_is_empty' => empty($email),
            'password_is_empty' => empty($password),
        ];

        if (!$email || !$password) {
            return new JsonResponse(['ok' => false, 'debug' => $debug]);
        }

        $user = $repo->findOneBy(['email' => $email]);
        $debug['user_found'] = $user !== null;

        if (!$user) {
            return new JsonResponse(['ok' => false, 'debug' => $debug]);
        }

        $debug['user_id'] = $user->getId();
        $debug['user_email'] = $user->getEmail();
        $debug['user_username'] = $user->getUsername();
        $debug['stored_hash'] = $user->getPassword();
        $debug['hash_length'] = strlen($user->getPassword() ?? '');

        $valid = $hasher->isPasswordValid($user, $password);
        $debug['password_valid'] = $valid;

        return new JsonResponse(['ok' => $valid, 'debug' => $debug]);
    }
}

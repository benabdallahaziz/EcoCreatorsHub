<?php
// src/Controller/Api/ResetPasswordApiController.php

namespace App\Controller\Api;  // ⭐⭐⭐ CE NAMESPACE EST CRITIQUE ⭐⭐⭐

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;

#[Route('/api/reset-password')]
class ResetPasswordApiController extends AbstractController
{
    #[Route('/request', name: 'api_forgot_password_request', methods: ['POST'])]
    public function request(
        Request $request,
        EntityManagerInterface $entityManager,
        ResetPasswordHelperInterface $resetPasswordHelper,
        MailerInterface $mailer
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'])) {
            return $this->json(['error' => 'Email requis'], Response::HTTP_BAD_REQUEST);
        }

        $user = $entityManager->getRepository(User::class)->findOneBy([
            'email' => $data['email'],
            'isActive' => true
        ]);

        if (!$user) {
            return $this->json([
                'message' => 'Si votre email existe, vous recevrez un lien de réinitialisation.'
            ], Response::HTTP_OK);
        }

        try {
            $resetToken = $resetPasswordHelper->generateResetToken($user);

            // ★★★ ENVOYER RÉELLEMENT L'EMAIL ★★★
            $resetUrl = $this->generateUrl('app_reset_password', [
                'token' => $resetToken->getToken()
            ], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL);

            $email = (new \Symfony\Component\Mime\Email())
                ->from('hello@demomailtrap.co')
                ->to($user->getEmail())
                ->subject('Réinitialisation de mot de passe - EcoCreatorsHub')
                ->text("Cliquez sur ce lien : $resetUrl")
                ->html("
                    <h1>Réinitialisation de mot de passe</h1>
                    <p><a href=\"$resetUrl\">$resetUrl</a></p>
                ");

            try {
                $mailer->send($email);
                $emailSent = true;
            } catch (\Exception $emailError) {
                $emailSent = false;
                error_log("Erreur email API: " . $emailError->getMessage());
            }

            return $this->json([
                'message' => 'Un email de réinitialisation a été envoyé.',
                'token' => $resetToken->getToken(),
                'email_sent' => $emailSent,
                'reset_url' => $resetUrl // Pour déboguer
            ], Response::HTTP_OK);

        } catch (ResetPasswordExceptionInterface $e) {
            return $this->json([
                'error' => 'Une erreur est survenue. ' . $e->getReason()
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Erreur: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // ⭐⭐⭐ AJOUTEZ CES MÉTHODES MANQUANTES ⭐⭐⭐

    #[Route('/validate-token', name: 'api_validate_token', methods: ['POST'])]
    public function validateToken(
        Request $request,
        ResetPasswordHelperInterface $resetPasswordHelper
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['token'])) {
            return $this->json(['error' => 'Token requis'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = $resetPasswordHelper->validateTokenAndFetchUser($data['token']);

            return $this->json([
                'valid' => true,
                'email' => $user->getEmail(),
                'username' => $user->getUsername()
            ], Response::HTTP_OK);

        } catch (ResetPasswordExceptionInterface $e) {
            return $this->json([
                'valid' => false,
                'error' => 'Token invalide ou expiré'
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/reset', name: 'api_reset_password', methods: ['POST'])]
    public function reset(
        Request $request,
        ResetPasswordHelperInterface $resetPasswordHelper,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['token']) || !isset($data['password'])) {
            return $this->json(['error' => 'Token et mot de passe requis'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = $resetPasswordHelper->validateTokenAndFetchUser($data['token']);

            if (strlen($data['password']) < 6) {
                return $this->json(
                    ['error' => 'Le mot de passe doit faire au moins 6 caractères'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);
            $entityManager->flush();

            $resetPasswordHelper->removeResetRequest($data['token']);

            return $this->json([
                'message' => 'Mot de passe réinitialisé avec succès',
                'user' => [
                    'email' => $user->getEmail(),
                    'username' => $user->getUsername()
                ]
            ], Response::HTTP_OK);

        } catch (ResetPasswordExceptionInterface $e) {
            return $this->json([
                'error' => 'Token invalide ou expiré'
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/check-email', name: 'api_check_email_exists', methods: ['POST'])]
    public function checkEmailExists(
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'])) {
            return $this->json(['error' => 'Email requis'], Response::HTTP_BAD_REQUEST);
        }

        $user = $entityManager->getRepository(User::class)->findOneBy([
            'email' => $data['email'],
            'isActive' => true
        ]);

        return $this->json([
            'exists' => $user !== null,
            'message' => $user !== null
                ? 'Email trouvé dans notre système'
                : 'Email non enregistré'
        ], Response::HTTP_OK);
    }

    // ⭐⭐⭐ ROUTE DE TEST POUR DÉBOGUER ⭐⭐⭐

    #[Route('/test-email-send', name: 'api_test_email_send', methods: ['GET'])]
    public function testEmailSend(MailerInterface $mailer): JsonResponse
    {
        try {
            $email = (new \Symfony\Component\Mime\Email())
                ->from('hello@demomailtrap.co')
                ->to('azizbenabdallah0412@gmail.com')
                ->subject('Test API Email - ' . date('H:i:s'))
                ->text('Ceci est un test depuis l\'API');

            $mailer->send($email);

            return $this->json([
                'success' => true,
                'message' => 'Email test envoyé avec succès'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
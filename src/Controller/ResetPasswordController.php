<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\TooManyPasswordRequestsException;

#[Route('/reset-password')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager
    ) {}
#[Route('/test-fixed-email', name: 'app_test_fixed_email')]
public function testFixedEmail(): Response
{
    // Test direct du template avec URL fixe
    $fakeToken = (object) [
        'token' => 'test-token-789',
        'expirationMessageKey' => '%count% hour',
        'expirationMessageData' => ['%count%' => 1]
    ];

    return $this->render('reset_password/email_fixed.html.twig', [
        'resetToken' => $fakeToken,
    ]);
}
    #[Route('', name: 'app_forgot_password_request')]
    public function request(): Response
    {
        return $this->render('reset_password/request.html.twig');
    }

    #[Route('/handle-request', name: 'app_forgot_password_handle', methods: ['POST'])]
    public function handle(Request $request, MailerInterface $mailer): Response
    {
        $emailInput = $request->request->get('email');

        if (!$emailInput) {
            $this->addFlash('error', 'Veuillez entrer votre email');
            return $this->redirectToRoute('app_forgot_password_request');
        }

        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => $emailInput]);

        // Message neutre pour la sécurité
        $this->addFlash('success', 'Si votre email existe, vous recevrez un lien.');

        if (!$user) {
            return $this->redirectToRoute('app_check_email');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
            $token = $resetToken->getToken();

            // ⭐⭐⭐⭐ URL FIXE ABSOLUE AVEC PORT 8000 ⭐⭐⭐⭐
            $resetUrl = 'http://localhost:8000/reset-password/reset/' . $token;

            // ⭐⭐⭐ CRÉATION MANUELLE DE L'EMAIL (sans utiliser le bundle) ⭐⭐⭐
            $email = (new TemplatedEmail())
                ->from(new Address(
                    $_ENV['MAILER_FROM_EMAIL'] ?? 'no-reply@ecocreatorshub.com',
                    $_ENV['MAILER_FROM_NAME'] ?? 'EcoCreatorsHub'
                ))
                ->to($user->getEmail())
                ->subject('Réinitialisation de votre mot de passe - EcoCreatorsHub')
                ->htmlTemplate('reset_password/email_fixed.html.twig')  // ⭐ NOUVEAU TEMPLATE ⭐
                ->context([
                    'resetToken' => $resetToken,
                    'resetUrl' => $resetUrl,
                ]);

            $mailer->send($email);

            // Pour debug : affichez l'URL dans la page
            $this->addFlash('info', 'Lien envoyé (debug) : ' . $resetUrl);
            $this->addFlash('success', '📧 Email envoyé avec succès !');

        } catch (TooManyPasswordRequestsException $e) {
            $this->addFlash('error', '⏳ Vous avez déjà demandé une réinitialisation récemment.');
        } catch (\Exception $e) {
            error_log('❌ Erreur email: ' . $e->getMessage());
            $this->addFlash('error', 'Erreur lors de l\'envoi de l\'email.');
        }

        return $this->redirectToRoute('app_check_email');
    }

    #[Route('/check-email', name: 'app_check_email')]
    public function checkEmail(): Response
    {
        return $this->render('reset_password/check_email.html.twig');
    }

    #[Route('/reset/{token}', name: 'app_reset_password')]
    public function reset(Request $request, UserPasswordHasherInterface $passwordHasher, string $token = null): Response
    {
        if ($token) {
            $this->storeTokenInSession($token);
            return $this->redirectToRoute('app_reset_password');
        }

        $token = $this->getTokenFromSession();
        if (!$token) {
            $this->addFlash('error', 'Token manquant ou expiré');
            return $this->redirectToRoute('app_forgot_password_request');
        }

        if ($request->isMethod('POST')) {
            $password = $request->request->get('password');
            $confirm = $request->request->get('confirm_password');

            if ($password !== $confirm) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->render('reset_password/reset.html.twig', [
                    'token' => $token,
                ]);
            }

            if (strlen($password) < 6) {
                $this->addFlash('error', 'Le mot de passe doit contenir au moins 6 caractères.');
                return $this->render('reset_password/reset.html.twig', [
                    'token' => $token,
                ]);
            }

            try {
                $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
                $user->setPassword($passwordHasher->hashPassword($user, $password));
                $this->entityManager->flush();
                $this->resetPasswordHelper->removeResetRequest($token);

                $this->cleanSessionAfterReset();

                $this->addFlash('success', '✅ Mot de passe réinitialisé avec succès !');
                return $this->redirectToRoute('app_login');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Token invalide ou expiré.');
                return $this->redirectToRoute('app_forgot_password_request');
            }
        }

        return $this->render('reset_password/reset.html.twig', [
            'token' => $token,
        ]);
    }
}
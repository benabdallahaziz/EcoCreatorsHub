<?php
// src/Security/LoginAuthenticator.php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');
        $password = $request->request->get('password', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        // Vérifier si l'utilisateur existe et est actif
        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        if ($user && !$user->isActive()) {
            throw new CustomUserMessageAuthenticationException('Votre compte est désactivé.');
        }

        if ($user && $user->hasRole('ROLE_BANNED')) {
            throw new CustomUserMessageAuthenticationException('Votre compte a été banni.');
        }

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();

        // Mettre à jour la dernière connexion
        if ($user instanceof User) {
            $user->setUpdatedAt(new \DateTime());
            $this->entityManager->flush();
        }

        // Rediriger vers la page précédente ou vers la page appropriée selon le rôle
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Redirection par rôle
        if ($user instanceof User) {
            if ($user->hasRole('ROLE_ADMIN')) {
                return new RedirectResponse($this->urlGenerator->generate('app_admin_user_index'));
            } elseif ($user->hasRole('ROLE_ARTIST')) {
                return new RedirectResponse($this->urlGenerator->generate('app_artist_dashboard'));
            }
        }

        // Par défaut vers la home page
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
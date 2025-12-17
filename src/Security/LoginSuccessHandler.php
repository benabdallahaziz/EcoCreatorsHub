<?php
// src/Security/LoginSuccessHandler.php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $user = $token->getUser();

        if ($user instanceof User) {
            // Redirection selon le rôle
            if ($user->hasRole('ROLE_ADMIN')) {
                return new RedirectResponse($this->urlGenerator->generate('app_admin_user_index'));
            } elseif ($user->hasRole('ROLE_ARTIST')) {
                return new RedirectResponse($this->urlGenerator->generate('app_artist_dashboard'));
            }
        }

        // Par défaut vers la home page
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }
}
<?php
// src/Security/AccessDeniedHandler.php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Component\Security\Core\Security;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    private $urlGenerator;
    private $security;

    public function __construct(UrlGeneratorInterface $urlGenerator, Security $security)
    {
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {
        // Obtenir l'utilisateur actuel
        $user = $this->security->getUser();

        // Obtenir la session
        $session = $request->getSession();

        if (!$user) {
            // Non connecté -> rediriger vers login
            $session->getFlashBag()->add('warning', 'Veuillez vous connecter pour accéder à cette page.');

            // Stocker la page où l'utilisateur voulait aller
            $session->set('_security.main.target_path', $request->getUri());

            return new RedirectResponse($this->urlGenerator->generate('app_login'));
        }

        // Connecté mais pas les bons droits
        $session->getFlashBag()->add('error',
            'Vous n\'avez pas les permissions nécessaires pour accéder à cette page.'
        );

        // Rediriger selon le rôle
        if ($user->hasRole('ROLE_ADMIN')) {
            return new RedirectResponse($this->urlGenerator->generate('app_admin_user_index'));
        } elseif ($user->hasRole('ROLE_ARTIST')) {
            return new RedirectResponse($this->urlGenerator->generate('app_artist_dashboard'));
        }

        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }
}
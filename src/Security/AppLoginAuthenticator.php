<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Core\Security;

class AppLoginAuthenticator extends AbstractAuthenticator
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * Cette méthode vérifie si l'authenticator doit être utilisé pour la requête en cours.
     */
    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'app_login' && $request->isMethod('POST');
    }

    /**
     * Cette méthode gère l'authentification. Elle récupère les informations de l'utilisateur depuis la requête.
     */
    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $csrfToken = $request->request->get('_csrf_token');

        // Créer un passeport avec les informations de l'utilisateur
        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $csrfToken),
            ]
        );
    }

    /**
     * Si l'authentification réussit, rediriger vers la page d'accueil.
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Redirection vers /home après connexion réussie
        return new RedirectResponse($this->router->generate('home'));
    }

    /**
     * En cas d'échec de l'authentification, rediriger vers la page de login avec un message d'erreur.
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        // Stocke un message d'erreur dans la session
        $request->getSession()->set(Security::LAST_USERNAME, $request->request->get('email'));

        return new RedirectResponse($this->router->generate('app_login'));
    }

    /**
     * (Optionnel) Cette méthode peut gérer le point d'entrée de l'authentification.
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        // Rediriger l'utilisateur vers la page de login s'il tente d'accéder à une page protégée sans être authentifié
        return new RedirectResponse($this->router->generate('app_login'));
    }
}

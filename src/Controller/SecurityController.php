<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Récupérer l'erreur de connexion s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();

        // Dernier username utilisé pour remplir le champ de connexion
        $lastUsername = $authenticationUtils->getLastUsername();

        if (!$error) {
            $error = '';  // ou vous pouvez laisser `null`
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,  // Transmet 'last_username' à Twig
            'error' => $error,  // Transmet 'error' à Twig
        ]);
    }


    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Il n'y a pas besoin d'implémenter de logique ici, Symfony gère ça
        throw new \Exception('This should never be reached!');
    }
}
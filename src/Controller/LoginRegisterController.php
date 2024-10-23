<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class LoginRegisterController extends AbstractController
{
    

    #[Route('/login-register', name: 'login-register-form')]
    public function index(Request $request): Response
    {
        $error = $request->getSession()->get('error');
        // Réinitialiser l'erreur dans la session
        $request->getSession()->remove('error');
        
        // Afficher la page home avec les mangas
        return $this->render('login-register.html.twig', [
            'error' => $error
        ]);
    }
}

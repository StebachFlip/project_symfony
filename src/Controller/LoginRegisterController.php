<?php 

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\MangaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LoginRegisterController extends AbstractController
{
    

    #[Route('/login-register', name: 'login-register-form')]
    public function index(): Response
    {
        // Afficher la page home avec les mangas
        return $this->render('login-register.html.twig');
    }
}

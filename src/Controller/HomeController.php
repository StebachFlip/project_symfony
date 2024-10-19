<?php

namespace App\Controller;

use App\Repository\MangaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function index(MangaRepository $mangaRepository): Response
    {
        // Récupérer tous les mangas depuis le repository
        $mangas = $mangaRepository->findAll();

        // Afficher la page home avec les mangas
        return $this->render('base.html.twig', [
            'mangas' => $mangas,
        ]);
    }
}

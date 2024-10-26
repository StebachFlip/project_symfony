<?php 

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\MangaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function index(MangaRepository $mangaRepository, CategoryRepository $categoryRepository, Request $request, Security $security): Response
    {
        // Récupérer tous les mangas depuis le repository
        $mangas = $mangaRepository->findAll();
        $genres = $categoryRepository->findAll();

        // Vérifiez si l'utilisateur est authentifié
        $user = $security->getUser();
        //dd($user); 
        
        // Afficher la page home avec les mangas
        return $this->render('base.html.twig', [
            'mangas' => $mangas,
            'genres' => $genres,
            'user' => $user,
            'error' => null
        ]);
    }

    #[Route('/home/search', name: 'manga_search', methods: ['GET'])]
    public function search(Request $request, MangaRepository $mangaRepository): JsonResponse
    {
        // Récupérer le terme de recherche
        $name = $request->query->get('name', '');

        // Rechercher les mangas par nom
        $mangas = $mangaRepository->searchByName('%' . $name . '%');

        // Préparer les données pour la réponse JSON
        $mangaData = [];
        foreach ($mangas as $manga) {
            $mangaData[] = [
                'id' => $manga->getId(),
                'name' => $manga->getName(),
                'author' => $manga->getAuthor(),
                'price' => $manga->getPrice(),
                'description' => $manga->getDescription(),
                'stock' => $manga->getStock(),
                'status' => $manga->getStatus(),
                'rating' => $manga->getRating(),
                'link' => $manga->getLink(),
                'image' => $manga->getImage() ? [
                    'url' => $manga->getImage()->getUrl(),
                    'format' => $manga->getImage()->getFormat()
                ] : null,
            ];
        }

        return new JsonResponse($mangaData, 200);
    }
}

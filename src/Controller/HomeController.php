<?php 

namespace App\Controller;

use App\Repository\MangaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface; // Importer le service Serializer

class HomeController extends AbstractController
{
    private SerializerInterface $serializer; // Déclaration du service Serializer

    public function __construct(SerializerInterface $serializer) // Injection du service dans le constructeur
    {
        $this->serializer = $serializer;
    }

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

    #[Route('/home/search', name: 'manga_search', methods: ['GET'])]
public function search(Request $request, MangaRepository $mangaRepository): JsonResponse
{
    // Récupérer le terme de recherche
    $name = $request->query->get('name', '');

    // Rechercher les mangas par nom
    $mangas = $mangaRepository->findByName('%' . $name . '%');

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
            'image' => $manga->getImage() ? [
                'url' => $manga->getImage()->getUrl(),
                'format' => $manga->getImage()->getFormat()
            ] : null,
        ];
    }

    return new JsonResponse($mangaData, 200);
}

}

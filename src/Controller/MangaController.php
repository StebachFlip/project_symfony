<?php 
namespace App\Controller;

use App\Repository\MangaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MangaController extends AbstractController
{
    private $mangaRepository;

    public function __construct(MangaRepository $mangaRepository)
    {
        $this->mangaRepository = $mangaRepository;
    }

    #[Route('/manga/{link}', name: 'manga_details', methods: ['GET'])]
    public function details(string $link): Response
    {
        $manga = $this->mangaRepository->findManga($link);

        // Si le manga n'est pas trouvé, renvoie une page 404
        if (!$manga) {
            throw $this->createNotFoundException('Le manga n\'existe pas.');
        }

        // Renvoyer les données à la vue
        return $this->render('details.html.twig', [
            'manga' => $manga,
        ]);
    }
}

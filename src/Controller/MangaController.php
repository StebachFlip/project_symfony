<?php 
namespace App\Controller;

use App\Repository\MangaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

class MangaController extends AbstractController
{
    private $mangaRepository;

    public function __construct(MangaRepository $mangaRepository)
    {
        $this->mangaRepository = $mangaRepository;
    }

    #[Route('/manga/{link}', name: 'manga_details', methods: ['GET'])]
    public function details(string $link, Security $security): Response
    {
        $manga = $this->mangaRepository->findManga($link);
        
        $user = $security->getUser();

        if (!$manga) {
            throw $this->createNotFoundException('Le manga n\'existe pas.');
        }

        return $this->render('details.html.twig', [
            'manga' => $manga,
            'user' => $user,
            'error' => null
        ]);
    }
}

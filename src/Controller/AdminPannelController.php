<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CartItemRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Repository\MangaRepository;
use App\Repository\UserRepository;
use App\Repository\OrderRepository;
use App\Entity\CartItem;
use App\Entity\Manga;
use App\Entity\Card;
use App\Form\CardFormType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AdminPannelController extends AbstractController
{
    #[Route('/admin-pannel', name: 'admin_pannel')]
    public function index(MangaRepository $mangaRepository, UserRepository $userRepository, OrderRepository $orderRepository ,Security $security, Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response {
        // Récupération de l'user connecté
        $user = $security->getUser();

        if(!$user instanceof User) {
            return $this->redirectToRoute('home');
        }

        // Si l'utilisateur n'a pas les droits, on redirige vers la page d'accueil
        if(!$user->getRole()) {
            return $this->redirectToRoute('home');
        }

        // Récupérations des informations du pannel
        $mangas = $mangaRepository->findAll();
        $users = $userRepository->findAll();
        $orders = $orderRepository->findBy([], ['createdAt' => 'DESC']);


        
        return $this->render('admin-pannel.html.twig', [
            'mangas'=> $mangas,
            'users' => $users,
            'orders' => $orders
        ]);
    }

}
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
use App\Entity\CartItem;

class BasketController extends AbstractController
{
    #[Route('/basket', name: 'basket')]
    public function index(CartItemRepository $cartItemRepository, Security $security): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $security->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('home');
        }

        // Récupérer les items du panier pour l'utilisateur connecté
        $cartItems = $cartItemRepository->findByUser($user);
        //dd($cartItems);

        // Calculer le total du panier
        $totalPrice = array_reduce($cartItems, fn($sum, $item) => $sum + ($item->getQuantity() * $item->getManga()->getPrice()), 0);

        return $this->render('basket.html.twig', [
            'cartItems' => $cartItems,
            'totalPrice' => $totalPrice,
        ]);
    }

    #[Route('/cart/remove/{id}', name: 'app_cart_remove', methods: ['POST'])]
    public function remove(CartItem $cartItem, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();

        // Vérification d'autorisation
        if ($cartItem->getUser() !== $user) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        // Supprimer l'article
        $entityManager->remove($cartItem);
        $entityManager->flush();

        // Calculer le nouveau total
        $cartItems = $entityManager->getRepository(CartItem::class)->findBy(['user' => $user]);
        $newTotal = array_reduce($cartItems, fn($sum, $item) => $sum + ($item->getQuantity() * $item->getManga()->getPrice()), 0);

        return new JsonResponse(['success' => true, 'newTotal' => $newTotal], Response::HTTP_OK);
    }
}

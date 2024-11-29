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
use App\Entity\Manga;
use App\Entity\Card;
use App\Form\CardFormType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart')]
    public function index(CartItemRepository $cartItemRepository, Security $security, Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $user = $security->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('home');
        }
        
        $cardExist = false;
        $isProfilePage = false;
        $incorrect_cvv = $session->get('incorrect_cvv');
        $session->remove('incorrect_cvv');

        if($incorrect_cvv != true) {
            $incorrect_cvv = false;
        }

        $cartItems = $cartItemRepository->findByUser($user);

        // Calculer le total du panier en prenant en compte la quantité de chaque article
        $totalPrice = array_reduce(
            $cartItems,
            fn($sum, $item) => $sum + (
                floatval(str_replace(',', '.', $item->getManga()->getPrice())) * $item->getQuantity()
            ),
            0
        );

        // Chargement du formulaire de carte de credit
        $cards = $user->getCards()->toArray();

        // Génération du formulaire d'ajout de carte
        $card = new Card();
        $addCardForm = $this->createForm(CardFormType::class, $card);
        $addCardForm->handleRequest($request);
        
        foreach ($cards as $index => $card) {
            $form = $this->createForm(CardFormType::class, $card, [
                'action' => $this->generateUrl('create_order', ),
                'method' => 'POST',
            ]);
            $cardsWithForms[] = [
                'card' => $card,
                'form' => $form->createView(),
            ];
        }

        /*if ($cardForms->isSubmitted() && $cardForm->isValid()) {
            $card = $cardForms->getData();

            $existingCard = $entityManager->getRepository(Card::class)->findOneBy([
                'number' => $card->getNumber(),  
                'user' => $user                  
            ]);
    
            if ($existingCard) {
                $this->addFlash('error', 'Cette carte existe déjà pour cet utilisateur.');
                $cardExist = true;
            } else {
                $card->setUser($user);
                $entityManager->persist($card);
                $entityManager->flush();
    
                $this->addFlash('success', 'Carte ajoutée avec succès.');
                $success = true;
            }
        }*/

        return $this->render('cart.html.twig', [
            'cartItems' => $cartItems,
            'totalPrice' => $totalPrice,
            'isProfilePage' =>$isProfilePage,
            'cards'=> $cards,
            'cardsWithForms'=> $cardsWithForms,
            'addCardForm' => $addCardForm,
            'cardExist' => $cardExist,
            'incorrect_cvv' => $incorrect_cvv

        ]);
    }


    #[Route('/cart/increase/{id}', name: 'cart_increase', methods: ['POST'])]
    public function increaseQuantity(CartItem $cartItem, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($cartItem->getUser() !== $this->getUser()) {
            return new JsonResponse(['success' => false, 'error' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $stock = $cartItem->getManga()->getStock();
        if ($cartItem->getQuantity() >= $stock) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Stock maximum atteint'
            ]);
        }

        $cartItem->setQuantity($cartItem->getQuantity() + 1);
        $entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'newQuantity' => $cartItem->getQuantity(),
        ]);
    }

    #[Route('/cart/decrease/{id}', name: 'cart_decrease', methods: ['POST'])]
    public function decreaseQuantity(CartItem $cartItem, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($cartItem->getUser() !== $this->getUser()) {
            return new JsonResponse(['success' => false, 'error' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $newQuantity = max(1, $cartItem->getQuantity() - 1);
        $cartItem->setQuantity($newQuantity);
        $entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'newQuantity' => $newQuantity,
        ]);
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove', methods: ['DELETE'])]
    public function removeItem(CartItem $cartItem, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($cartItem->getUser() !== $this->getUser()) {
            return new JsonResponse(['success' => false, 'error' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $entityManager->remove($cartItem);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add', methods: ['POST'])]
    public function addToCart(Manga $manga, Request $request, Security $security, EntityManagerInterface $entityManager)
    {
        // Vérifier que l'utilisateur est authentifié
        $user = $security->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('home');
        }

        // Récupérer la quantité demandée (valeur de l'input "number")
        $quantity = (int)$request->request->get('quantity', 1); // Valeur par défaut 1 si la quantité est absente

        if ($quantity < 1) {
            return new JsonResponse(['status' => 'error', 'message' => 'Quantité invalide.'], 400);
        }

        // Vérifier que la quantité demandée ne dépasse pas le stock disponible
        if ($quantity > $manga->getStock()) {
            return new JsonResponse(['status' => 'error', 'message' => 'Quantité demandée supérieure au stock disponible.'], 400);
        }

        // Chercher si l'utilisateur a déjà un élément pour ce manga
        $cartItem = $entityManager->getRepository(CartItem::class)->findOneBy([
            'user' => $user,
            'manga' => $manga
        ]);

        if ($cartItem) {
            // Si le manga est déjà dans le panier, on met à jour la quantité
            $newQuantity = $cartItem->getQuantity() + $quantity;
            
            // Vérifier que la nouvelle quantité ne dépasse pas le stock
            if ($newQuantity > $manga->getStock()) {
                return new JsonResponse(['status' => 'error', 'message' => 'Quantité totale dans le panier dépasse le stock disponible.'], 400);
            }

            // Mettre à jour la quantité
            $cartItem->setQuantity($newQuantity);
        } else {
            // Sinon, on crée un nouvel élément dans le panier
            $cartItem = new CartItem();
            $cartItem->setUser($user);
            $cartItem->setManga($manga);
            $cartItem->setQuantity($quantity);
            $entityManager->persist($cartItem);
        }

        // Sauvegarder le panier et ses éléments
        $entityManager->flush();

        // Retourner une réponse JSON avec un message de succès
        return new JsonResponse([
            'status' => 'success',
            'message' => 'Produit ajouté au panier!',
            'cart_count' => count($entityManager->getRepository(CartItem::class)->findBy(['user' => $user])) // Nombre d'éléments dans le panier de l'utilisateur
        ]);
    }


    #[Route('/cart/count', name: 'cart_count', methods: ['GET'])]
    public function getCartCount(Security $security, CartItemRepository $cartItemRepository): JsonResponse
    {
        $user = $security->getUser();

        if (!$user) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Utilisateur non connecté'
            ]);
        }

        // Récupérer les éléments du panier de l'utilisateur
        $cartItems = $cartItemRepository->findBy(['user' => $user]);

        // Calculer le nombre total d'articles (quantité)
        $totalItems = array_reduce($cartItems, function ($carry, $cartItem) {
            return $carry + $cartItem->getQuantity(); // Ajoute la quantité de chaque item
        }, 0);

        return new JsonResponse([
            'status' => 'success',
            'cart_count' => $totalItems
        ]);
    }
    
    #[Route('/update_card', name: 'update_card', methods: ['POST'])]
    public function updateCard(Request $request, Card $card): Response
    {
        $form = $this->createForm(CardFormType::class, $card);
        $form->handleRequest($request);

        /*if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer les modifications
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Carte mise à jour.');

            return $this->redirectToRoute('payment_options');
        }

        return $this->render('payment/edit.html.twig', [
            'form' => $form->createView(),
            'card' => $card,
        ]);*/
        return $this->redirectToRoute('home');
    }
}

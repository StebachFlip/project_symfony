<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Card;
use App\Form\CardFormType;
use App\Repository\CartItemRepository;
use App\Enum\OrderStatus;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class OrderController extends AbstractController {
    
    #[Route('/create_order', name: 'create_order', methods: ['POST'])]
    public function createOrder(Request $request, EntityManagerInterface $entityManager, Security $security, CartItemRepository $cartItemRepository, SessionInterface $session): Response
    {
        $user = $security->getUser(); // Utilisateur connecté
        $cartItems = $cartItemRepository->findCartItemsByUser($user);

        // Vérifier si le panier est vide
        if (empty($cartItems)) {
            throw $this->createNotFoundException('Votre panier est vide.');
        }

        // Assurer que le formulaire existe
        $form = $this->createForm(CardFormType::class, null, [
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données validées du formulaire
            $formData = $form->getData(); // formData est un tableau associatif
    
            // Extraire les champs spécifiques du formulaire
            $cvv = $formData->getCvv();
    
            // Récupérer l'ID de la carte sélectionnée
            $selectedCardId = $request->request->get('card_id');
    
            // Trouver la carte dans la base de données
            $card = $entityManager->getRepository(Card::class)->find($selectedCardId);
    
            if (!$card) {
                throw $this->createNotFoundException('Carte introuvable.');
            }
    
            // Vérifier que le CVV est correct
            if ($card->getCvv() !== $cvv) {
                $session->set('incorrect_cvv', true);
                return $this->redirectToRoute('cart');
            }
        
            // Création de la commande
            $order = new Order();
            $order->setUser($user);
            $order->setReference(uniqid());
            $order->setCreatedAt(new \DateTimeImmutable());
            $order->setStatus(OrderStatus::PENDING);

            $totalPrice = 0;

            foreach ($cartItems as $cartItem) {
                $manga = $cartItem->getManga();
        
                if ($manga->getStock() < $cartItem->getQuantity()) {
                    throw $this->createNotFoundException(sprintf(
                        'Stock insuffisant pour %s. Seulement %d en stock.',
                        $manga->getTitle(),
                        $manga->getStock()
                    ));
                }
        
                // Création de l'OrderItem
                $orderItem = new OrderItem();
                $orderItem->setOrder($order);
                $orderItem->setManga($manga);
                $orderItem->setQuantity($cartItem->getQuantity());
                $orderItem->setProductPrice($manga->getPrice() * $cartItem->getQuantity());
        
                $totalPrice += $orderItem->getProductPrice();
        
                // Réduction du stock
                $manga->setStock($manga->getStock() - $cartItem->getQuantity());
        
                $entityManager->persist($orderItem);
            }

            $entityManager->persist($order);

            // Supprimer les articles du panier après la commande
            foreach ($cartItems as $cartItem) {
                $entityManager->remove($cartItem);
            }

            $entityManager->flush();

        }
        
        $session->set('order_success', true);
        return $this->redirectToRoute('profile');
    }
}


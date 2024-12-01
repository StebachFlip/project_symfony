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
use App\Entity\Order;
use App\Repository\MangaRepository;
use App\Repository\UserRepository;
use App\Repository\OrderRepository;
use App\Entity\CartItem;
use App\Entity\Manga;
use App\Entity\Card;
use App\Form\CardFormType;
use IntlDateFormatter;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AdminPannelController extends AbstractController
{
    private function getDistinctMangaSales(array $items)
    {
        $mangaSales = [];

        foreach ($items as $item) {
            $manga = $item->getManga();
        
            if ($manga === null) {
                // Affichage pour déboguer
                dump($item); // Afficher l'item qui ne possède pas de manga
                continue; // Passer à l'élément suivant
            }
        
            $mangaId = $manga->getId();
        
            if (!isset($mangaSales[$mangaId])) {
                $mangaSales[$mangaId] = [
                    'name' => $manga->getName(),
                    'quantity' => 0,
                    'productPrice' => $item->getProductPrice(),
                ];
            }
        
            // Ajouter la quantité au manga existant
            $mangaSales[$mangaId]['quantity'] += $item->getQuantity();
        }

        // Retourner les mangas avec leurs quantités et prix
        return array_values($mangaSales);
    }

    
    
    
    #[Route('/admin-pannel', name: 'admin_pannel')]
    public function index(MangaRepository $mangaRepository, UserRepository $userRepository, OrderRepository $orderRepository ,Security $security, Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response {
        // Récupération de l'user connecté
        $user = $security->getUser();
    
        if (!$user instanceof User) {
            return $this->redirectToRoute('home');
        }
    
        // Si l'utilisateur n'a pas les droits, on redirige vers la page d'accueil
        if (!$user->getRole()) {
            return $this->redirectToRoute('home');
        }

        $suppr = $session->get('suppr');
        $session->remove('suppr');

        $error = $session->get('error');
        $session->remove('error');

        if($suppr != true) {
            $suppr = false;
        }
    
        // Récupérations des informations du pannel
        $mangas = $mangaRepository->findAll();
        $users = $userRepository->findAll();
        $orders_list = $orderRepository->findBy([], ['createdAt' => 'DESC']);
    
        // Récupération des chiffres d'affaires
        $now = new \DateTime();
    
        // Créer un tableau pour stocker les montants totaux par mois
        $monthlySales = [];
    
        // Récupérer les commandes des 12 derniers mois
        $orders = $entityManager->getRepository(Order::class)
                                ->createQueryBuilder('o')
                                ->where('o.createdAt >= :lastYear')
                                ->setParameter('lastYear', $now->modify('-12 months'))
                                ->orderBy('o.createdAt', 'ASC')
                                ->getQuery()
                                ->getResult();
    
        // Filtrer et calculer le montant total des ventes par mois
        foreach ($orders as $order) {
            // On récupère l'année et le mois de la commande
            $monthYear = $order->getCreatedAt()->format('Y-m');
    
            // Initialiser le mois si ce n'est pas déjà fait
            if (!isset($monthlySales[$monthYear])) {
                $monthlySales[$monthYear] = [
                    'total' => 0,
                    'items' => [],
                ];
            }
    
            // Parcourir les items de la commande
            foreach ($order->getOrderItems() as $item) {
                // Vérifier si le manga existe dans cet item
                $manga = $item->getManga();
                if ($manga === null) {
                    // Si pas de manga, on ignore cet item
                    continue;
                }
    
                // Ajouter le prix du produit à la vente totale
                $monthlySales[$monthYear]['total'] += $item->getProductPrice();
    
                // Ajouter l'ID du manga aux items distincts
                $monthlySales[$monthYear]['items'][$manga->getId()] = [
                    'name' => $manga->getName(),
                    'quantity' => $item->getQuantity(),
                    'productPrice' => $item->getProductPrice(),
                    'image' => $manga->getImage() ? $manga->getImage()->getUrl() : null, // Ajout de l'URL de l'image
                ];
            }
        }
    
        // Préparer les données pour le template
        $salesData = [];
        foreach ($monthlySales as $monthYear => $data) {
            $salesData[] = [
                'month' => $monthYear,
                'total' => $data['total'],
                'distinctMangasSold' => count($data['items']),
                'items' => $data['items'], // Inclure ici les items
            ];
        }

        // Extraire les mois et les montants pour le graphique
        $chartLabels = [];
        $chartData = [];
        foreach ($salesData as $monthData) {
            $date = \DateTime::createFromFormat('Y-m', $monthData['month']); // Créer un objet DateTime
            $formattedDate = $date->format('F Y'); // Exemple : January 2024
            $chartLabels[] = $formattedDate; // Ajouter la date formatée
            $chartData[] = $monthData['total']; // Montant total des ventes
        }
    
        return $this->render('admin-pannel.html.twig', [
            'mangas' => $mangas,
            'users' => $users,
            'orders_list' => $orders_list,
            'salesData' => $salesData,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'suppr' => $suppr,
            'error' => $error
        ]);
    }
}
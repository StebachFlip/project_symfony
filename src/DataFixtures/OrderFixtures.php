<?php

namespace App\DataFixtures;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\User;
use App\Entity\Manga;
use App\Enum\OrderStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $now = new \DateTime('2024-11-30'); // Date actuelle
        $usersCount = 10; // Nombre d'utilisateurs créés
        $mangasCount = 20; // Nombre de mangas créés dans vos fixtures `MangaFixtures`

        // Générer 12 mois de commandes
        for ($i = 0; $i < 12; $i++) {
            // Date de la commande (un mois en arrière à chaque itération)
            $orderDate = (clone $now)->modify("-{$i} months");

            // Créer une commande
            $order = new Order();
            $order->setReference(uniqid());
            $order->setCreatedAt($orderDate);
            $order->setStatus(OrderStatus::COMPLETED); // Statut de commande
            $order->setUser($this->getReference('user-' . rand(1, $usersCount), User::class)); // Associer un utilisateur aléatoire

            // Ajouter des `OrderItem` à la commande
            $itemsCount = rand(1, 5); // Nombre aléatoire d'items par commande
            for ($j = 0; $j < $itemsCount; $j++) {
                $orderItem = new OrderItem();
                $orderItem->setQuantity(rand(1, 10)); // Quantité entre 1 et 10
                $orderItem->setProductPrice(rand(5, 50)); // Prix entre 5€ et 50€
                $orderItem->setManga($this->getReference('manga_' . rand(0, $mangasCount - 1), Manga::class)); // Manga aléatoire
                $orderItem->setOrder($order);

                $manager->persist($orderItem);
            }

            $manager->persist($order);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            MangaFixtures::class,
        ];
    }
}

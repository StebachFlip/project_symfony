<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AddressFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Créer l'adresse pour l'administrateur
        $admin = $this->getReference('admin-user', User::class);
        $adminAddress = new Address();
        $adminAddress->setStreet('1 Admin Street');
        $adminAddress->setPostalCode(12345);
        $adminAddress->setCity('Admin City');
        $adminAddress->setCountry('Admin Country');
        $adminAddress->setUser($admin);

        // Persist l'adresse de l'administrateur
        $manager->persist($adminAddress);

        // Créer des adresses pour les 10 utilisateurs
        for ($i = 1; $i <= 10; $i++) {
            $user = $this->getReference("user-$i", User::class);
            $address = new Address();
            $address->setStreet("$i User Street");
            $address->setPostalCode(12345 + $i);
            $address->setCity("City$i");
            $address->setCountry("Country$i");
            $address->setUser($user);

            // Persist chaque adresse
            $manager->persist($address);
        }

        // Flush des adresses dans la base de données
        $manager->flush();
    }

    public function getDependencies():array
    {
        // Retourner la dépendance à UserFixtures
        return [
            UserFixtures::class,
        ];
    }
}

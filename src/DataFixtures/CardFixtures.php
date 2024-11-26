<?php

namespace App\DataFixtures;

use App\Entity\Card;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CardFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $card = new Card();
        $card->setUser(user: $this->getReference('admin-user'));
        $card->setNumber(1111222233334444);
        $card->setExpirationDate("10/24");
        $card->setCvv(026);
        $manager->persist($card);
        $manager->flush();

    }

    // Cette méthode permet de définir les dépendances
    public function getDependencies(): array
    {
        return [
            UserFixtures::class, // Remplace par le nom de ta classe de fixture d'utilisateur
        ];
    }
}

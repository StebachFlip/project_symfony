<?php

namespace App\DataFixtures;

use App\Entity\ProfilePicture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProfilPictureFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $profilePicture = new ProfilePicture();
            $profilePicture->setUser(user: $this->getReference('user-' . $i)); // On suppose que les utilisateurs ont été créés et référencés
            $profilePicture->setPath('path/to/profile_picture_' . $i . '.jpg');

            $manager->persist($profilePicture);
        }

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

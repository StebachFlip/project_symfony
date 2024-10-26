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
        $profilePicture = new ProfilePicture();
        $profilePicture->setUser(user: $this->getReference('admin-user'));
        $profilePicture->setPath('pp/lucioleuwu.png');
        $manager->persist($profilePicture);
        
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

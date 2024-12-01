<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Création de l'administrateur
        $admin = new User();
        $admin->setEmail('loic.@gmail.com');
        $admin->setName('StebachFlip');
        $admin->setFirstname('Loic');
        $admin->setLastname(lastname: 'Stebach');
        $admin->setRole(true);

        // Hashage du mot de passe de l'administrateur
        $hashedPassword = $this->passwordHasher->hashPassword($admin, 'loic57350');
        $admin->setPassword($hashedPassword);

        // Persist l'admin dans le gestionnaire d'entité
        $manager->persist($admin);

        // Référence à l'administrateur pour utilisation dans AddressFixtures
        $this->addReference('admin-user', object: $admin);

        // Création de 10 utilisateurs
        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setEmail("user$i@example.com");
            $user->setName("User$i");
            $user->setFirstname("Firstname$i");
            $user->setLastname("Lastname$i");
            $user->setRole(false);

            // Hashage du mot de passe pour chaque utilisateur
            $hashedPassword = $this->passwordHasher->hashPassword($user, "userpassword$i");
            $user->setPassword($hashedPassword);

            // Persiste chaque utilisateur
            $manager->persist($user);

            // Référence à chaque utilisateur pour utilisation dans AddressFixtures
            $this->addReference("user-$i", $user);
        }

        // Flush des entités dans la base de données
        $manager->flush();
    }
}

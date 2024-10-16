<?php

namespace App\DataFixtures;

use App\Entity\Manga;
use App\Enum\MangaStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class MangaFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Création des mangas
        for ($i = 1; $i <= 10; $i++) {
            $manga = new Manga();
            $manga->setName("Manga Title $i");
            $manga->setAuthor("Author $i");
            $manga->setPrice(mt_rand(5, 30));  // Prix aléatoire
            $manga->setDescription("Description du manga $i");
            $manga->setStock(mt_rand(0, 1) === 1);  // Stock aléatoire
            
            if ($manga->getStock() > 0) {
                $manga->setStatus(MangaStatus::AVAILABLE);
            } else {
                $manga->setStatus(MangaStatus::UNAVAILABLE);
            }

            // Ajouter des catégories aléatoires
            $categories = [
                $this->getReference('category_Shonen'),
                $this->getReference('category_Seinen'),
                $this->getReference('category_Shojo')
            ];

            // Ajout de 1 à 3 catégories aléatoires
            $randomCategories = array_rand($categories, mt_rand(1, 3));
            foreach ((array) $randomCategories as $categoryKey) {
                $manga->addCategory($categories[$categoryKey]);
            }

            // Sauvegarder la référence pour pouvoir l'utiliser dans d'autres fixtures (ex: ImageFixtures)
            $this->addReference('manga_' . $i, $manga);

            // Persister le manga
            $manager->persist($manga);
        }

        $manager->flush();
    }

    // Spécifie que MangaFixtures dépend de CategoryFixtures pour s'assurer que les catégories sont chargées en premier
    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}

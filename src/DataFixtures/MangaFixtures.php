<?php

namespace App\DataFixtures;

use App\Entity\Manga;
use App\Entity\Image;
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
            $manga->setAuthor("jaaj");
            $manga->setPrice(mt_rand(5, 30)); // Prix aléatoire entre 5 et 30
            $manga->setDescription("Description du manga $i");
            $manga->setStock(mt_rand(0, 1) === 1); // Stock aléatoire (en stock ou non)
            
            // Statut du manga
            if($manga->getStock() > 0) {
                $manga->setStatus(status: MangaStatus::AVAILABLE);
            }
            else {
                $manga->setStatus(status: MangaStatus::UNAVAILABLE);
            }
            
            // Ajout de l'image
            $image = new Image();
            $image->setUrl("/images/manga$i.jpg");
            $image->setFormat('jpg');
            $manga->setImage($image);

            $manager->persist($image);

            // Ajout de 1 à 3 catégories aléatoires à partir des catégories créées dans CategoryFixtures
            $categoryReferences = [
                $this->getReference('category_Shonen'),
                $this->getReference('category_Seinen'),
                $this->getReference('category_Shojo')
            ];

            $randomCategories = array_rand($categoryReferences, mt_rand(1, 3));
            foreach ((array) $randomCategories as $categoryKey) {
                $manga->addCategory($categoryReferences[$categoryKey]);
            }

            $manager->persist($manga);
        }

        $manager->flush();
    }

    // Dépendance à la fixture CategoryFixtures
    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}

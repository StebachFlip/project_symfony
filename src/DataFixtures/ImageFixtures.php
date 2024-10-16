<?php

namespace App\DataFixtures;

use App\Entity\Image;
use App\Entity\Manga;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ImageFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Récupérer les mangas créés dans MangaFixtures
        for ($i = 1; $i <= 10; $i++) {
            $image = new Image();
            $image->setUrl("/images/manga$i.jpg");
            $image->setFormat('jpg');

            // Récupérer le manga correspondant à partir de la référence de MangaFixtures
            $manga = $this->getReference('manga_' . $i);
            $image->setManga($manga);  // Associe l'image au manga

            $manager->persist($image);

            // Mettre à jour la relation dans Manga
            $manga->setImage($image);
            $manager->persist($manga);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            MangaFixtures::class, // Charger les mangas avant les images
        ];
    }
}

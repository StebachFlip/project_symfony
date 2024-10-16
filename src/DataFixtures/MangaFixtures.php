<?php

namespace App\DataFixtures;

use App\Entity\Manga;
use App\Enum\MangaStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MangaFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $manga = new Manga();
            $manga->setName("Manga Title $i");
            $manga->setAuthor("jaaj");
            $manga->setPrice(mt_rand(5, 30));
            $manga->setDescription("Description du manga $i");
            $manga->setStock(mt_rand(0, 1) === 1);
            
            if ($manga->getStock() > 0) {
                $manga->setStatus(MangaStatus::AVAILABLE);
            } else {
                $manga->setStatus(MangaStatus::UNAVAILABLE);
            }

            // Sauvegarder la référence pour pouvoir l'utiliser dans ImageFixtures
            $this->addReference('manga_' . $i, $manga);

            $manager->persist($manga);
        }

        $manager->flush();
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Manga;
use App\Entity\Category;
use App\Enum\mangaStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MangaFixtures extends Fixture
{
    public function load(ObjectManager $manager):void
    {
        $jsonContent = file_get_contents(__DIR__ . '/Data/mangas.json');
        $mangas = json_decode($jsonContent, true);
        
        $categories = $manager->getRepository(Category::class)->findAll();
        $categoryMap = [];
        
        foreach ($categories as $category) {
            $categoryMap[$category->getName()] = $category;
        }

        foreach ($mangas as $index => $mangaData) {
            $manga = new Manga();
            $manga->setName($mangaData['name']);
            $manga->setAuthor($mangaData['author']);
            $manga->setPrice($mangaData['price']);
            $manga->setDescription($mangaData['description']);
            $manga->setStock($mangaData['stock']);
            $manga->setRating($mangaData['ratings']);
            $manga->setLink($mangaData['link']);

            if ($mangaData['stock'] > 0) {
                $manga->setStatus(mangaStatus::AVAILABLE);
            } else {
                $manga->setStatus(mangaStatus::UNAVAILABLE);
            }

            foreach ($mangaData['genres'] as $genreName) {
                if (isset($categoryMap[$genreName])) {
                    $manga->addCategory($categoryMap[$genreName]);
                }
            }

            $this->addReference('manga_' . $index, $manga);

            $manager->persist($manga);
        }

        $manager->flush();
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ImageFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $jsonContent = file_get_contents(__DIR__ . '/Data/image.json');
        $images = json_decode($jsonContent, true);
        
        foreach ($images as $index => $imageData) {
            $mangaReference = 'manga_' . $index;
            
            if ($this->hasReference($mangaReference)) {
                $manga = $this->getReference($mangaReference);
                
                $image = new Image();
                $image->setManga($manga);
                $image->setUrl($imageData['path']); 
                $image->setFormat($imageData['format']); 

                $manager->persist($image);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            MangaFixtures::class,
        ];
    }
}
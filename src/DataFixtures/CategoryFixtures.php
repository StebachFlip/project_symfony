<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = ['Shonen', 'Seinen', 'Shojo'];

        foreach ($categories as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $manager->persist($category);

            // On crée une référence pour que MangaFixtures puisse y accéder
            $this->addReference("category_$categoryName", $category);
        }

        $manager->flush();
    }
}

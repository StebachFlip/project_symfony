<?php 
namespace App\Repository;

use App\Entity\Manga;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MangaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Manga::class);
    }

    // Méthode pour rechercher les mangas par nom
    public function findByName(string $name): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT m
            FROM App\Entity\Manga m
            WHERE m.name LIKE :name'
        )->setParameter('name', $name); // Corrigez ici en utilisant ':name'

        return $query->getResult();
    }
}

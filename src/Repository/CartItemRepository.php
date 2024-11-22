<?php

namespace App\Repository;

use App\Entity\CartItem;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CartItem>
 */
class CartItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartItem::class);
    }

    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('ci')
            ->andWhere('ci.user = :user')
            ->setParameter('user', $user)
            ->leftJoin('ci.manga', 'manga')
            ->addSelect('manga')
            ->getQuery()
            ->getResult();
    }


}

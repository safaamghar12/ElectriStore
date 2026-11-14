<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function countProducts(): int
    {
      return $this->count([]);
    }

    public function countOutOfStock(): int
    {
        return $this->count(['stock' => 0]);
    }

    public function countLowStock(): int
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.stock > 0')
            ->andWhere('p.stock <= 5')
            ->getQuery()
            ->getSingleScalarResult();
    }
}

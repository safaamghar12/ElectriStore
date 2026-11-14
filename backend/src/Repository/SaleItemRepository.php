<?php

namespace App\Repository;

use App\Entity\Sale;
use App\Entity\SaleItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SaleItem>
 */
class SaleItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SaleItem::class);
    }

    /**
     * @return SaleItem[]
     */
    public function findBySale(Sale $sale): array
    {
        return $this->createQueryBuilder('si')
            ->andWhere('si.sale = :sale')
            ->setParameter('sale', $sale)
            ->orderBy('si.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

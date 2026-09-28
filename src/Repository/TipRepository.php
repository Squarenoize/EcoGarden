<?php

namespace App\Repository;

use App\Entity\Tip;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tip>
 */
class TipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tip::class);
    }

    public function findByMonth(int $monthNumber): array
    {
        return $this->createQueryBuilder('t')
            ->innerJoin('t.months', 'm')
            ->andWhere('m.number = :monthNumber')
            ->setParameter('monthNumber', $monthNumber)
            ->getQuery()
            ->getResult();
    }
}

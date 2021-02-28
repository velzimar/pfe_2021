<?php

namespace App\Repository;

use App\Entity\DealCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DealCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method DealCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method DealCategory[]    findAll()
 * @method DealCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DealCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DealCategory::class);
    }

    public function findByUser($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.businessId = :val')
            ->setParameter('val', $value)
            //->orderBy('p.id', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }
}

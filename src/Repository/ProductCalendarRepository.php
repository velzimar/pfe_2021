<?php

namespace App\Repository;

use App\Entity\ProductCalendar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductCalendar|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductCalendar|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductCalendar[]    findAll()
 * @method ProductCalendar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductCalendarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductCalendar::class);
    }

    // /**
    //  * @return ProductCalendar[] Returns an array of ProductCalendar objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProductCalendar
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

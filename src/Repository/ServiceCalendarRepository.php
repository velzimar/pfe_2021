<?php

namespace App\Repository;

use App\Entity\ServiceCalendar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ServiceCalendar|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServiceCalendar|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServiceCalendar[]    findAll()
 * @method ServiceCalendar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceCalendarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceCalendar::class);
    }

    // /**
    //  * @return ServiceCalendar[] Returns an array of ServiceCalendar objects
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
    public function findOneBySomeField($value): ?ServiceCalendar
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

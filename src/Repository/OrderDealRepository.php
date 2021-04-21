<?php

namespace App\Repository;

use App\Entity\OrderDeal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrderDeal|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderDeal|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderDeal[]    findAll()
 * @method OrderDeal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderDealRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderDeal::class);
    }

    // /**
    //  * @return OrderDeal[] Returns an array of OrderDeal objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OrderDeal
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

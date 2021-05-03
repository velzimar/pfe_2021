<?php

namespace App\Repository;

use App\Entity\OrderProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrderProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderProduct[]    findAll()
 * @method OrderProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderProduct::class);
    }

    // /**
    //  * @return OrderProduct[] Returns an array of OrderProduct objects
    //  */

    /*'TIMESTAMPDIFF(MINUTE, CURRENT_DATE(), o.orderDate) as diff',*/
    public function findLast4($value)
    {
        return $this->createQueryBuilder('o')
            ->addSelect("
            CASE 
                WHEN(TIMESTAMPDIFF(MINUTE, o.orderDate, :now) >= 1440) THEN CONCAT(TIMESTAMPDIFF(DAY, o.orderDate, :now),'jours')
                WHEN(TIMESTAMPDIFF(MINUTE, o.orderDate, :now) >= 60) THEN CONCAT(TIMESTAMPDIFF(HOUR, o.orderDate, :now),' heures')
                ELSE CONCAT(TIMESTAMPDIFF(MINUTE, o.orderDate, :now),' minutes')
            END as diff")
            ->andWhere('o.business = :val')
            ->setParameter('val', $value)
            ->setParameter('now', new \DateTime('now'))
            ->orderBy('o.orderDate', 'DESC')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult()
        ;
    }
    public function findLast4Clients($value)
    {
         $a = $this->getEntityManager()->createQueryBuilder();
         $a->select(array(
             "Distinct c.id",
             " c.email as email ",
             " o.phone as phone",
             " Max(o.id) as orderId",
             " Count(o.id) as count",

         ))
            ->from('App\Entity\OrderProduct','o')
             ->from('App\Entity\User','c')
             ->andWhere('c.id = o.client')
            ->andWhere('o.business = :val')
            ->setParameter('val', $value)
            ->groupBy('c.id')
            ->orderBy('o.orderDate', 'ASC')
            ->setMaxResults(4)
            ;
        return  $a->getQuery()->getResult();
    }

    /*
    public function findOneBySomeField($value): ?OrderProduct
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

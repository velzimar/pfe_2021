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
            ->orderBy('o.orderDate', 'DESC')
            ->setMaxResults(4)
            ;
        return  $a->getQuery()->getResult();
    }

    public function findByUserByStatus($clientId,$status)
    {
        $a = $this->getEntityManager()->createQueryBuilder();
        $a->select(array(
            "o.id as orderId",
            "o.total  ",
            "o.orderDate  ",
            "o.modifyDate  ",
            "o.status  ",
            "o.seen  ",
            "Identity(o.business) as business",
            "b.businessName as businessName",
            "o.delivery as delivery",
        ))
            ->from('App\Entity\User','b')
            ->from('App\Entity\OrderProduct','o')
            ->andWhere('b.id = o.business')
            ->andWhere('o.client = :clientId')
            ->andWhere('o.status = :status')
            ->orderBy('o.orderDate', 'DESC')
            ->setParameter('clientId', $clientId)
            ->setParameter('status', $status)
        ;
        return  $a->getQuery()->getResult();
    }


    public function findCustomersOrderByMostBought($date,$user)
    {
        return $this->createQueryBuilder('so')
            ->select(array(
                "so.id as orderId",
                "u.email",
                "Identity(so.client) as ClientId ",
                "Sum(so.total) as revenu",
                "Count(so.id) as nbcmd",
            ))
            ->from("App\Entity\User","u")
            ->andWhere('u.id = so.client')
            ->andWhere(':date <= so.orderDate')
            ->andWhere(':business = so.business')
            ->setParameter('date',$date)
            ->setParameter('business',$user)
            ->groupBy("so.client")
            ->orderBy("revenu","DESC")
            ->getQuery()->getResult();

    }
    public function findSommeTotal($date,$user)
    {
        return $this->createQueryBuilder('so')


            ->select(
                " Sum(so.total) as somme"
            )
            ->andWhere(':date <= so.orderDate')
            ->andWhere(':business = so.business')
            ->setParameter('date',$date)
            ->setParameter('business',$user)
            ->getQuery()->getResult();

    }
    public function findCustomers($date,$user)
    {
        return $this->createQueryBuilder('so')


            ->select(
                "  COUNT ( DISTINCT so.client ) as somme"
            )
            ->andWhere(':date <= so.orderDate')
            ->andWhere(':business = so.business')
            ->setParameter('date',$date)
            ->setParameter('business',$user)
            ->getQuery()->getResult();

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

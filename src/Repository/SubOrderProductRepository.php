<?php

namespace App\Repository;

use App\Entity\SubOrderProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SubOrderProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubOrderProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubOrderProduct[]    findAll()
 * @method SubOrderProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubOrderProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubOrderProduct::class);
    }

    // /**
    //  * @return SubOrderProduct[] Returns an array of SubOrderProduct objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SubOrderProduct
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findByUser_withSubOrder($orderId)
    {
        return $this->createQueryBuilder('so')
        ->select(array(
            "so.id as subOrderId",
            "so.options",
            "so.optionsPrice as prixOp",
            "so.modifyDate as sub_mdate",
            "so.qtt",
            "Identity(so.product) as productId",
            "so.name",
            "so.price",
            "so.status",
        ))
            ->andWhere(':order = so.orderProduct')
            ->setParameter('order',$orderId)
           ->getQuery()->getResult();

    }
}

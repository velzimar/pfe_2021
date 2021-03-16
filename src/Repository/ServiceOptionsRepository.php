<?php

namespace App\Repository;

use App\Entity\ServiceOptions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ServiceOptions|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServiceOptions|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServiceOptions[]    findAll()
 * @method ServiceOptions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceOptionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceOptions::class);
    }

    // /**
    //  * @return ServiceOptions[] Returns an array of ServiceOptions objects
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
    public function findOneBySomeField($value): ?ServiceOptions
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

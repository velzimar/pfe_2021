<?php

namespace App\Repository;

use App\Entity\Deal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Deal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Deal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Deal[]    findAll()
 * @method Deal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DealRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Deal::class);
    }


    public function findByUser($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.business = :val')
            ->setParameter('val', $value)
            //->orderBy('p.id', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByUserByCategory($user,$category)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.business = :user')
            ->andWhere('p.category = :category')
            ->setParameter('user', $user)
            ->setParameter('category', $category)
            //->orderBy('p.id', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }

}

<?php

namespace App\Repository;

use App\Entity\Geolocation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Geolocation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Geolocation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Geolocation[]    findAll()
 * @method Geolocation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GeolocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Geolocation::class);
    }

    // /**
    //  * @return Geolocation[] Returns an array of Geolocation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Geolocation
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findOneClientById($id)
    {

        $query = $this->getEntityManager()->createQueryBuilder();
        $fields = array('p.longitude', 'p.latitude', 'r.id as id','r.email as email');
        $query
            ->select($fields)
            ->from('App\Entity\Geolocation', 'p')
            ->join('p.user', 'r')
            ->andWhere('p.user = r.id')
            ->andWhere('p.user = :id')
            ->setParameter("id",$id)
        ;
        $results = $query->getQuery()->getResult();
        return $results;
    }
}

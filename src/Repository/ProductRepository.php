<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */

    public function findByUser($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.business = :val')
            ->setParameter('val', $value)
            //->orderBy('p.id', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findByUserByCategory($user, $category)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.business = :user')
            ->andWhere('p.category = :category')
            ->setParameter('user', $user)
            ->setParameter('category', $category)
            //->orderBy('p.id', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }



    public function find10Products()
    {
        return $this->createQueryBuilder('p')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }


    public function findMaxPriority($user, $category)
    {
        return $this->createQueryBuilder('p')
            ->select('MAX(p.priority) AS max_priority')
            ->andWhere('p.business = :user')
            ->andWhere('p.category = :category')
            ->setParameter('user',$user)
            ->setParameter('category',$category)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    public function findMinPriority($user, $category)
    {
        return $this->createQueryBuilder('p')
            ->select('MIN(p.priority) AS min_priority')
            ->andWhere('p.business = :user')
            ->andWhere('p.category = :category')
            ->setParameter('user',$user)
            ->setParameter('category',$category)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    public function findProductsOfThisCategory($user, $category)
    {
        return $this->createQueryBuilder('p')
            ->select('p.id as id, p.nom as nom, p.prix as prix , p.priority as priority')
            ->andWhere('p.business = :user')
            ->andWhere('p.category = :category')
            ->setParameter('user',$user)
            ->setParameter('category',$category)
            ->orderBy("priority","DESC")
            ->getQuery()
            ->getResult();
    }

    public function findByName($nom)
    {

        $query = $this->getEntityManager()->createQueryBuilder();
        $fields = array('p.nom as name', 'p.prix as price','p.description', 'p.id', 'r.id as business', 'c.id as category');
        $query
            ->select($fields)
            ->from('App\Entity\Product', 'p')
            ->join('p.business', 'r')
            ->join('p.category', 'c')
            ->andWhere('p.business = r.id')
            ->andWhere('p.category = c.id')
            ->andWhere('p.nom LIKE :nom')
            ->setParameter('nom', "$nom%");

        $results = $query->getQuery()->getResult();
        return $results;
    }
    public function findByBusinessIdByName($id,$nom)
    {

        $query = $this->getEntityManager()->createQueryBuilder();
        $fields = array('p.nom as name', 'p.prix as price','p.description', 'p.id', 'r.id as business', 'c.id as category');
        $query
            ->select($fields)
            ->from('App\Entity\Product', 'p')
            ->join('p.business', 'r')
            ->join('p.category', 'c')
            ->andWhere('p.business = r.id')
            ->andWhere('p.category = c.id')
            ->andWhere('p.business = :id')
            ->andWhere('p.nom LIKE :nom')
            ->setParameter('nom', "$nom%")
            ->setParameter('id', $id);

        $results = $query->getQuery()->getResult();
        return $results;
    }

    /*
    public function findOneBySomeField($value): ?Product
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

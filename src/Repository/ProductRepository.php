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


    public function findByName($nom)
    {

        $query = $this->getEntityManager()->createQueryBuilder();
        $fields = array('p.nom', 'p.prix', 'p.id', 'r.id as business', 'c.id as category');
        $query
            ->select($fields)
            ->from('App\Entity\Product', 'p')
            ->join('p.business', 'r')
            ->join('p.category', 'c')
            ->andWhere('p.business = r.id')
            ->andWhere('p.category = c.id');
        if ($nom !== "") {
            $query
                ->andWhere('p.nom = :nom')
                ->setParameter('nom', $nom);
        }else{
            $query->setMaxResults(10);
        }
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

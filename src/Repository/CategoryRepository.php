<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    // /**
    //  * @return Category[] Returns an array of Category objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    
    public function findAllAPI()
    {
        return $this->createQueryBuilder('c')
            ->select('c.id as id, c.nom as icon , c.description as text')
            ->getQuery()
            ->getResult()
        ;
    }
    public function findAllAPINotEmpty()
    {
           
        return $this->createQueryBuilder('c')
            ->select('c.id as id, c.nom as icon , c.description as text, u.id as business, Count(Distinct s.category) as nbCategories')
            ->from('App\Entity\User', 'u')
            ->from('App\Entity\Product', 's')
            ->addSelect('COUNT(Distinct u.CategoryId) AS nbBusinesses')
            ->addSelect('COUNT(s.business) AS nbProducts')
            ->andWhere('u.id = s.business')
            ->andWhere('c.id = u.CategoryId')
            ->groupBy('c.id')//,u.id')
            ->having('nbBusinesses > 0')
            ->andHaving('nbProducts > 0')
            ->getQuery()
            ->getResult()
        ;
    }
    public function findByBusinessNameAPINotEmpty($searchParam)
    {
        return $this->createQueryBuilder('c')
            ->select('c.id as id, c.nom as icon, c.description as text, u.businessName as name')
            ->from('App\Entity\User', 'u')
            ->from('App\Entity\Product', 's')
            ->addSelect('COUNT(s.business) AS nbProducts')
            ->andWhere('u.id = s.business')
           //->join('u.CategoryId','c')
            ->andWhere('c.id = u.CategoryId')
            ->andWhere('u.businessName LIKE :searchParam')
            ->groupBy('c.id,u.id')
            ->andHaving('nbProducts > 0')
            //->andHaving('u.businessName LIKE :searchParam')
            ->setParameter('searchParam',"$searchParam%")
            ->getQuery()
            ->getResult()
        ;
    }
}

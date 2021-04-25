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

    public function findByBusinessIdByName($id,$nom,$path)
    {

        $query = $this->getEntityManager()->createQueryBuilder();
        $fields = array('p.nom as name', 'p.real_price as prixReel', 'p.prix as price','(1-(p.prix/p.real_price))*100 as ratio','p.description', 'p.id', 'r.id as business', 'c.id as category',"COALESCE(CONCAT('{$path}',p.filename),'{$path}default.jpg') as path", 'p.end_date as fin', 'p.date_add as debut');
        $query
            ->select($fields)
            ->from('App\Entity\Deal', 'p')
            ->join('p.business', 'r')
            ->join('p.category', 'c')
            ->andWhere('p.business = r.id')
            ->andWhere('p.category = c.id')
            ->andWhere('p.business = :id')

            ->andWhere('0 < p.qtt')
            ->andWhere('p.end_date > p.date_add')
            ->andWhere('CURRENT_TIMESTAMP() < p.end_date')
            ->andWhere('p.nom LIKE :nom')
            ->setParameter('nom', "$nom%")
            ->setParameter('id', $id);

        $results = $query->getQuery()->getResult();
        return $results;
    }
    public function findByBusinessIdByCategoryIdByName($id,$nom,$path,$categoryId)
    {

        $query = $this->getEntityManager()->createQueryBuilder();
        $fields = array('p.nom as name', 'p.real_price as prixReel', 'p.prix as price','(1-(p.prix/p.real_price))*100 as ratio','p.description', 'p.id', 'r.id as business', 'c.id as category',"COALESCE(CONCAT('{$path}',p.filename),'{$path}default.jpg') as path", 'p.end_date as fin', 'p.date_add as debut');
        $query
            ->select($fields)
            ->from('App\Entity\Deal', 'p')
            ->join('p.business', 'r')
            ->join('p.category', 'c')
            ->andWhere('p.business = r.id')
            ->andWhere('p.category = c.id')
            ->andWhere('p.business = :id')
            ->andWhere('p.nom LIKE :nom')
            ->andWhere('p.category = :categoryId')
            ->andWhere('p.end_date > p.date_add')
            ->andWhere('0 < p.qtt')
            ->andWhere('CURRENT_TIMESTAMP() < p.end_date')
            ->setParameter('nom', "$nom%")
            ->setParameter('categoryId', $categoryId)
            ->setParameter('id', $id);

        $results = $query->getQuery()->getResult();
        return $results;
    }
    public function findBusinessInfosByDealId($id)
    {

        $query = $this->getEntityManager()->createQueryBuilder();
        $fields = array('r.id as businessId', 'r.businessName ', 'r.phone as businessPhone', 'r.email as businessEmail');
        $query
            ->select($fields)
            ->from('App\Entity\Deal', 'p')
            ->join('p.business', 'r')
            ->andWhere('p.business = r.id')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id);

        $results = $query->getQuery()->getResult();
        return $results;
    }

}

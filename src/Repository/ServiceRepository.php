<?php

namespace App\Repository;

use App\Entity\Service;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Service|null find($id, $lockMode = null, $lockVersion = null)
 * @method Service|null findOneBy(array $criteria, array $orderBy = null)
 * @method Service[]    findAll()
 * @method Service[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Service::class);
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
    public function find4AvailableByCategory($category)
    {
        return $this->createQueryBuilder('p')
            ->select("p.id,p.nom, p.prix, u.businessName")
            ->from('App\Entity\User','u')
            ->from('App\Entity\ServiceCalendar','sc')
            ->from('App\Entity\WorkingHours','wh')
            ->andWhere('p.business = u.id')
            ->andWhere('wh.business = u.id')
            ->andWhere(':category = u.CategoryId')
            ->andWhere('p.id = sc.service')
            ->andWhere(' sc.isActive = 1')
            ->setParameter('category',$category)
            //->orderBy('p.id', 'ASC')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult()
            ;
    }
    public function findAvailableByCategory($category)
    {
        return $this->createQueryBuilder('p')
            ->select("p.id,p.nom, p.prix, u.businessName")
            ->from('App\Entity\User','u')
            ->from('App\Entity\ServiceCalendar','sc')
            ->from('App\Entity\WorkingHours','wh')
            ->andWhere('p.business = u.id')
            ->andWhere('wh.business = u.id')
            ->andWhere(':category = u.CategoryId')
            ->andWhere('p.id = sc.service')
            ->andWhere(' sc.isActive = 1')
            ->setParameter('category',$category)
            //->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
    public function findAvailableByCategoryByName($category,$name)
    {
        return $this->createQueryBuilder('p')
            ->select("p.id,p.nom, p.prix, u.businessName")
            ->from('App\Entity\User','u')
            ->from('App\Entity\ServiceCalendar','sc')
            ->from('App\Entity\WorkingHours','wh')
            ->andWhere('p.business = u.id')
            ->andWhere('wh.business = u.id')
            ->andWhere(':category = u.CategoryId')
            ->andWhere('p.nom LIKE :name')
            ->andWhere('p.id = sc.service')
            ->andWhere(' sc.isActive = 1')
            ->setParameter('category',$category)
            ->setParameter('name',"%".$name."%")
            //->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
    public function findAvailableByCategoryByNameNearby($category,$name)
    {
        return $this->createQueryBuilder('p')
            ->select("p.id,p.nom, p.prix, u.businessName, u.longitude, u.latitude")
            ->from('App\Entity\User','u')
            ->from('App\Entity\ServiceCalendar','sc')
            ->from('App\Entity\WorkingHours','wh')
            ->andWhere('p.business = u.id')
            ->andWhere('wh.business = u.id')
            ->andWhere(':category = u.CategoryId')
            ->andWhere('p.nom LIKE :name')
            ->andWhere('p.id = sc.service')
            ->andWhere(' sc.isActive = 1')
            ->setParameter('category',$category)
            ->setParameter('name',"%".$name."%")
            //->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
}

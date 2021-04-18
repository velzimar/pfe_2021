<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
    /**
     * @param string $role
     *
     * @return array
     */
    public function findByRole($role)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
            ->from($this->_entityName, 'u')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"'.$role.'"%')
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @param string $role
     * @param $role2
     * @return array
     */
    public function findByRoleNot($role,$role2)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
            ->from($this->_entityName, 'u')
            ->andWhere('u.roles NOT LIKE :roles')
            ->andWhere('u.roles NOT LIKE :roles2')
            ->setParameter('roles', '%"'.$role.'"%')
            ->setParameter('roles2', '%"'.$role2.'"%')
        ;

        return $qb->getQuery()->getResult();
    }
    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findOnePersonalInfoById($id)
    {
        $fields = array('p.nom', 'p.prenom', 'p.cin', 'p.phone');
        $qb = $this->_em->createQueryBuilder();
        $qb->select($fields)
            ->from($this->_entityName, 'p')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id);
        return $qb->getQuery()->getResult();
    }
    public function findOneGeolocationById($id)
    {
        $fields = array('p.longitude', 'p.latitude');
        $qb = $this->_em->createQueryBuilder();
        $qb->select($fields)
            ->from($this->_entityName, 'p')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id);
        return $qb->getQuery()->getResult();
    }

    public function findTop4ForEachCategory($id,$name)
    {
    $fields = array('p.id', 'p.businessName');
        $qb = $this->_em->createQueryBuilder();
        $qb->select($fields)
            ->from($this->_entityName, 'p')
            ->from('App\Entity\ProductCategory', 's')
            ->andWhere('p.CategoryId = :id')
            ->andWhere('p.businessName LIKE :name')
            ->addSelect('COUNT(s.businessId) AS nbCategories')
            ->andWhere('p.id = s.businessId')
            ->groupBy('p.id')
            ->orderBy('nbCategories','DESC')
            ->setMaxResults(4)
            ->setParameter('id', $id)
            ->setParameter('name', "$name%");
        return $qb->getQuery()->getResult();
    }
    public function findTop4ForEachCategoryNotEmptyDeals($id,$name)
    {
        $fields = array('p.id', 'p.businessName');
        $qb = $this->_em->createQueryBuilder();
        $qb->select($fields)
            ->from($this->_entityName, 'p')
           // ->from('App\Entity\DealCategory', 's')
            ->from('App\Entity\Deal', 'd')
            ->addSelect('COUNT(d.business) AS nbProducts')
            ->andWhere('p.id = d.business')
            ->andWhere('p.CategoryId = :id')
            ->andWhere('p.businessName LIKE :name')
         //   ->addSelect('COUNT(s.businessId) AS nbCategories')
          //  ->andWhere('p.id = s.businessId')
            ->groupBy('p.id')
            ->andHaving('nbProducts > 0')
            ->orderBy('nbProducts','DESC')
            ->setMaxResults(4)
            ->setParameter('id', $id)
            ->setParameter('name', "$name%");
        return $qb->getQuery()->getResult();
    }

    //all businesses of a category ( only businesses that have products)
    public function findBusinessesOfACategory($id)
    {
    $fields = array('p.id', 'p.businessName','p.longitude','p.latitude');
        $qb = $this->_em->createQueryBuilder();
        $qb->select($fields)
            ->from($this->_entityName, 'p')
            ->from('App\Entity\Product', 's')
            ->andWhere('p.CategoryId = :id')  
            ->addSelect('COUNT(s.business) AS nbProducts')
            ->andWhere('p.id = s.business')
            ->groupBy('p.id')
            ->orderBy('nbProducts','DESC')
            ->setParameter('id', $id);
        return $qb->getQuery()->getResult();
    }
     //all businesses of a category by name of business ( only businesses that have products)
     public function findBusinessesOfACategoryByName($id,$name)
     {
     $fields = array('p.id', 'p.businessName','p.longitude','p.latitude');
         $qb = $this->_em->createQueryBuilder();
         $qb->select($fields)
             ->from($this->_entityName, 'p')
             ->from('App\Entity\Product', 's')
             ->andWhere('p.CategoryId = :id')  
             ->addSelect('COUNT(s.business) AS nbProducts')
             ->andWhere('p.id = s.business')
             ->andWhere('p.businessName LIKE :name')
             ->groupBy('p.id')
             ->orderBy('nbProducts','DESC')
             ->setParameter('id', $id)
             ->setParameter('name', "$name%");
         return $qb->getQuery()->getResult();
     }
     //all businesses of a category by name of business ( only businesses that have products)
     public function findBusinessesOfACategoryByNameWithDelivery($id,$name)
     {
     $fields = array('p.id', 'p.businessName','p.longitude','p.latitude');
         $qb = $this->_em->createQueryBuilder();
         $qb->select($fields)
             ->from($this->_entityName, 'p')
             ->from('App\Entity\Product', 's')
             ->from('App\Entity\Delivery', 'd')
             ->andWhere('p.CategoryId = :id')  
             ->addSelect('COUNT(s.business) AS nbProducts')
             ->andWhere('p.id = s.business')
             ->andWhere('d.user = p.id')
             ->andWhere('d.isActive = 1')
             ->andWhere('p.businessName LIKE :name')
             ->groupBy('p.id')
             ->orderBy('nbProducts','DESC')
             ->setParameter('id', $id)
             ->setParameter('name', "$name%");
         return $qb->getQuery()->getResult();
     }



     //for deals
    //all businesses of a category by name of business ( only businesses that have deals)
    public function findBusinessesOfACategoryByNameNotEmptyDeals($id,$name)
    {
        $fields = array('p.id', 'p.businessName','p.longitude','p.latitude');
        $qb = $this->_em->createQueryBuilder();
        $qb->select($fields)
            ->from($this->_entityName, 'p')
            ->from('App\Entity\Deal', 's')
            ->andWhere('p.CategoryId = :id')
            ->addSelect('COUNT(s.business) AS nbProducts')
            ->andWhere('p.id = s.business')
            ->andWhere('p.businessName LIKE :name')
            ->groupBy('p.id')
            ->orderBy('nbProducts','DESC')
            ->setParameter('id', $id)
            ->setParameter('name', "$name%");
        return $qb->getQuery()->getResult();
    }
    //all businesses of a category by name of business ( only businesses that have deals)
    public function findBusinessesOfACategoryByNameWithDeliveryNotEmptyDeals($id,$name)
    {
        $fields = array('p.id', 'p.businessName','p.longitude','p.latitude');
        $qb = $this->_em->createQueryBuilder();
        $qb->select($fields)
            ->from($this->_entityName, 'p')
            ->from('App\Entity\Deal', 's')
            ->from('App\Entity\Delivery', 'd')
            ->andWhere('p.CategoryId = :id')
            ->addSelect('COUNT(s.business) AS nbProducts')
            ->andWhere('p.id = s.business')
            ->andWhere('d.user = p.id')
            ->andWhere('d.isActive = 1')
            ->andWhere('p.businessName LIKE :name')
            ->groupBy('p.id')
            ->orderBy('nbProducts','DESC')
            ->setParameter('id', $id)
            ->setParameter('name', "$name%");
        return $qb->getQuery()->getResult();
    }

}

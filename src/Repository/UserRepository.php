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
    public function findTop4ForEachCategory($id)
    {
    $fields = array('p.id', 'p.businessName');
        $qb = $this->_em->createQueryBuilder();
        $qb->select($fields)
            ->from($this->_entityName, 'p')
            ->from('App\Entity\ProductCategory', 's')
            ->andWhere('p.CategoryId = :id')  
            ->addSelect('COUNT(s.businessId) AS nbCategories')
            ->andWhere('p.id = s.businessId')
            ->groupBy('p.id')
            ->orderBy('nbCategories','DESC')
            ->setMaxResults(4)
            ->setParameter('id', $id);
        return $qb->getQuery()->getResult();
    }
}

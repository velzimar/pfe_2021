<?php

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    // /**
    //  * @return Notification[] Returns an array of Notification objects
    //  */

    public function findNotSeenById($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.receiver = :val')
            ->andWhere('n.seen = :seen')
            ->setParameter('val', $value)
            ->setParameter('seen', 0)
            ->orderBy('n.date', 'ASC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?Notification
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


    /**
     * @param $userId
     * @return Notification[] Returns an array of Notification objects
     */
    public function findByUser($userId): array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.Receiver = :val')
            ->setParameter('val', $userId)
            ->orderBy('n.sending_date','desc')
            ->getQuery()
            ->getResult()
            ;
    }
}

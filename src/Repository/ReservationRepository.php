<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    // /**
    //  * @return Reservation[] Returns an array of Reservation objects
    //  */

    public function findReservationAtThisTime($time,$service,$day)
    {
        return $this->createQueryBuilder('r')
            ->select('r.id, r.status, Identity(r.client) as client , r.phone')
            ->andWhere('r.service = :service')
            ->andWhere('r.selectedDate LIKE :time')
            ->andWhere('r.status != :status')
            ->andWhere('DayOfWeek(r.selectedDate) = :day')
            ->setParameter('time', '%'.$time)
            ->setParameter('day', $day)
            ->setParameter('status', "Annuler")
            ->setParameter('service', $service)
            ->orderBy('r.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    public function findReservationsByServiceByStatus($service,$status)
    {
        return $this->createQueryBuilder('r')
            ->select('r.id, r.status, Identity(r.client) as client , r.phone, r.selectedDate as date')
            ->andWhere('r.service = :service')
            ->andWhere('r.status != :status')
            ->andWhere('r.selectedDate > CURRENT_TIMESTAMP()')
            ->setParameter('status', $status)
            ->setParameter('service', $service)
            ->orderBy('r.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
    public function findWithSameDateNotCancelled($service,$date,$id)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.service = :service')
            ->andWhere('r.status != :status')
            ->andWhere('r.id != :id')
            ->andWhere('r.selectedDate = :date')
            ->setParameter('date', $date)
            ->setParameter('id', $id)
            ->setParameter('service', $service)
            ->setParameter('status', "Annuler")
            ->orderBy('r.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findReservationAtThisDay($date,$service,$repeat,$timeRange)
    {
        $likeOp = $repeat?"_____".substr($date,-5).'%': $date.'%';
        if($timeRange==""){
        return $this->createQueryBuilder('r')
            ->select('r.id, r.status, Identity(r.client) as client , r.phone, r.selectedDate as date')
            ->andWhere('r.service = :service')
            ->andWhere('r.selectedDate LIKE :time')
            ->andWhere('r.status != :status')
            ->setParameter('time', $likeOp)
            ->setParameter('status', "Annuler")
            ->setParameter('service', $service)
            ->orderBy('r.id', 'ASC')
            ->getQuery()
            ->getResult();

        } else{
            $date1 = $date." ".substr($timeRange,0,5).':00';
            $date2 = $date." ".substr($timeRange,-5).':00';

            $from = new \DateTime($date1);
            $to   = new \DateTime($date2);


            dump($likeOp);
            dump($date1);
            dump($date2);
            dump($from);
            dump($to);


            if(!$repeat)
            return $this->createQueryBuilder('r')
                ->select('r.id, r.status, Identity(r.client) as client , r.phone')
                ->andWhere('r.service = :service')
                ->andWhere('r.selectedDate LIKE :time')
                ->andWhere('r.status != :status')
                ->andWhere("r.selectedDate >= :from AND r.selectedDate < :to OR DATE_ADD(r.selectedDate,15, 'minute')>:to AND r.selectedDate < :from")
                ->setParameter('from', $from)
                ->setParameter('to', $to)
                ->setParameter('time', $likeOp)
                ->setParameter('status', "Annuler")
                ->setParameter('service', $service)
                ->orderBy('r.id', 'ASC')
                ->getQuery()
                ->getResult();
            else{

                $from = new \DateTime($date1);
                $to   = new \DateTime($date2);
                $from   = substr($from->format('Y-m-d H:i:s'),11,5);
                $to   = substr($to->format('Y-m-d H:i:s'),11,5);

                dump($from);
                dump($to);
                return $this->createQueryBuilder('r')
                    ->select('r.id, r.status, Identity(r.client) as client , r.phone')
                    ->andWhere('r.service = :service')
                    ->andWhere('r.selectedDate LIKE :time')
                    ->andWhere('r.status != :status')
                    ->andWhere("CAST(SUBSTRING(r.selectedDate, 12, 8) AS time) >= CAST(:from AS time) AND CAST(SUBSTRING(r.selectedDate, 12, 8) AS time) < CAST(:to AS time) OR CAST(SUBSTRING(DATE_ADD(r.selectedDate,15, 'minute'), 12, 8) AS time) > CAST(:to AS time) AND CAST(SUBSTRING(r.selectedDate, 12, 8) AS time)  < CAST(:from AS time)")
                    ->setParameter('from', $from)
                    ->setParameter('to', $to)
                    ->setParameter('time', $likeOp)
                    ->setParameter('status', "Annuler")
                    ->setParameter('service', $service)
                    ->orderBy('r.id', 'ASC')
                    ->getQuery()
                    ->getResult();
            }

        }
    }

    /*
    public function findOneBySomeField($value): ?Reservation
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

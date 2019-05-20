<?php

namespace App\Repository;

use App\Entity\Prenotazione;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Prenotazione|null find($id, $lockMode = null, $lockVersion = null)
 * @method Prenotazione|null findOneBy(array $criteria, array $orderBy = null)
 * @method Prenotazione[]    findAll()
 * @method Prenotazione[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrenotazioneRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Prenotazione::class);
    }

    public function findIdsPrenotati($user)
    {
        $qb = $this->createQueryBuilder('p');

        return $qb->select('p.id')
            ->where('p.user = :user')
            ->andWhere('p.start >= :now')
            ->setParameter('user', $user)
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getResult();
    }

    public function findPrenotazioneWeek($start, $end)
    {
        $qb = $this->createQueryBuilder('p');

        return $qb->where('p.start >= :start')
            ->andWhere('p.end <= :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->select('p.id','p.start', 'p.end', 'p.title')
            ->getQuery()
            ->getResult();
    }

    public function findPrenotazioneOggi($user)
    {
        $qb = $this->createQueryBuilder('p');

        return $qb->select('p.id')
            ->where('p.timestamp >= :oggi')
            ->andWhere('p.timestamp <= :domani')
            ->andWhere('p.user = :user')
            ->andWhere('p.start >= :now')
            ->setParameter('oggi', \DateTime::createFromFormat( "Y-m-d H:i:s", date("Y-m-d 00:00:00")))
            ->setParameter('domani', \DateTime::createFromFormat( "Y-m-d H:i:s", date("Y-m-d 23:59:59")))
            ->setParameter('now', new \DateTime())
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

    }

    /**
     * @param $end \DateTime
     * @param $start \DateTime
     * @return Prenotazione[]
     */
    public function findOverlap($start, $end)
    {
        $qb = $this->createQueryBuilder('p');

        return $qb->where('p.start > :start')
            ->andWhere('p.start < :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Prenotazione[] Returns an array of Prenotazione objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Prenotazione
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

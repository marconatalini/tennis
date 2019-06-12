<?php

namespace App\Repository;

use App\Entity\Contatto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Contatto|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contatto|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contatto[]    findAll()
 * @method Contatto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContattoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Contatto::class);
    }

    // /**
    //  * @return Contatto[] Returns an array of Contatto objects
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

    /*
    public function findOneBySomeField($value): ?Contatto
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

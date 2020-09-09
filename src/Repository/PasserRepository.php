<?php

namespace App\Repository;

use App\Entity\Passer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Passer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Passer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Passer[]    findAll()
 * @method Passer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PasserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Passer::class);
    }

    // /**
    //  * @return Passer[] Returns an array of Passer objects
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
    public function findOneBySomeField($value): ?Passer
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

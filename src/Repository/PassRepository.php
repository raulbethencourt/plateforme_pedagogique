<?php

namespace App\Repository;

use App\Entity\Pass;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pass|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pass|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pass[]    findAll()
 * @method Pass[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PassRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pass::class);
    }

    // /**
    //  * @return Pass[] Returns an array of Pass objects
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
    public function findOneBySomeField($value): ?pass
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

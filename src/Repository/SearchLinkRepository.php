<?php

namespace App\Repository;

use App\Entity\SearchLink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SearchLink|null find($id, $lockMode = null, $lockVersion = null)
 * @method SearchLink|null findOneBy(array $criteria, array $orderBy = null)
 * @method SearchLink[]    findAll()
 * @method SearchLink[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SearchLinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SearchLink::class);
    }

    // /**
    //  * @return SearchLink[] Returns an array of SearchLink objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SearchLink
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

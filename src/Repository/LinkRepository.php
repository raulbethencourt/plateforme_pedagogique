<?php

namespace App\Repository;

use App\Entity\Link;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Link|null find($id, $lockMode = null, $lockVersion = null)
 * @method Link|null findOneBy(array $criteria, array $orderBy = null)
 * @method Link[]    findAll()
 * @method Link[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Link::class);
    }

    /**
     * This method allows to find a link by creator or visibility.
     */
    public function findByVisibilityOrCreator(string $creator): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.visibility = 1')
            ->orWhere('l.creator = :val')
            ->setParameter('val', $creator)
            ->orderBy('l.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    //TODO crear la requet pour search
}

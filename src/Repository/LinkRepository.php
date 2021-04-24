<?php

namespace App\Repository;

use App\Entity\Link;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\ArrayCollection;
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
    public function findByVisibilityOrCreator(bool $visibility, string $creator): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.visibility = :val1')
            ->orWhere('l.creator = :val2')
            ->setParameters(new ArrayCollection([
                new Parameter('val1', $visibility),
                new Parameter('val2', $creator),
            ]))
            ->orderBy('l.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}

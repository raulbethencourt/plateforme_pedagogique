<?php

namespace App\Repository;

use App\Entity\Link;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    /**
     * This method find the links with search bar data.
     */
    public function findBySearch(?string $name, ?string $category, ?string $creator): array
    {
        $query = $this->createQueryBuilder('l');

        if (isset($name)) {
            $query = $query->andWhere('l.name LIKE :name')
                ->setParameter('name', "%{$name}%")
            ;
        }

        if (isset($category)) {
            $query = $query->andWhere('l.category = :category')
                ->setParameter('category', $category)
            ;
        }

        if (isset($creator)) {
            $query = $query->andWhere('l.creator = :creator')
                ->setParameter('creator', $creator)
            ;
        }

        return $query->orderBy('l.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}

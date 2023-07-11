<?php

namespace App\Repository;

use App\Entity\Questionnaire;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Questionnaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Questionnaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Questionnaire[]    findAll()
 * @method Questionnaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionnaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Questionnaire::class);
    }

    /**
     * This method allows to find a questionnaire by an Id.
     *
     * @throws NonUniqueResultException
     */
    public function findOneById(string $id): ?questionnaire
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * This method allows to find a questionnaire by creator or visibility.
     */
    public function findByVisibilityOrCreator(bool $visibility, string $creator): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.visibility = :val1')
            ->orWhere('q.creator = :val2')
            ->setParameters(new ArrayCollection([
                new Parameter('val1', $visibility),
                new Parameter('val2', $creator),
            ]))
            ->orderBy('q.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * This method find the links with search bar data.
     */
    public function findBySearch(?string $title, ?string $level, ?string $category, ?string $creator, ?DateTime $date): array
    {
        $query = $this->createQueryBuilder('l');

        if (isset($title)) {
            $query = $query->andWhere('l.title LIKE :title')
                ->setParameter('title', "%{$title}%")
            ;
        }

        if (isset($level)) {
            $query = $query->andWhere('l.level = :level')
                ->setParameter('level', $level)
            ;
        }

        if (isset($category)) {
            $query = $query->andWhere('l.difficulty = :category')
                ->setParameter('category', $category)
            ;
        }

        if (isset($creator)) {
            $query = $query->andWhere('l.creator = :creator')
                ->setParameter('creator', $creator)
            ;
        }

        if (isset($date)) {
            $query = $query->andWhere('l.date_creation = :date')
                ->setParameter('date', $date)
            ;
        }

        return $query->orderBy('l.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}

<?php

namespace App\Repository;

use DateTime;
use App\Entity\Lesson;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Lesson|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lesson|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lesson[]    findAll()
 * @method Lesson[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LessonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lesson::class);
    }

    /**
     * @return Lesson[] Returns an array of Lesson objects
     */
    public function findByClassroom($classroom): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.classrooms LIKE :val')
            ->setParameter('val', "%{$classroom}%")
            ->orderBy('l.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * This method allows to find a questionnaire by creator or visibility.
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

    /**
     * This method find the links with search bar data.
     */
    public function findBySearch(?string $title, ?string $level, ?string $creator, ?DateTime $date): array
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

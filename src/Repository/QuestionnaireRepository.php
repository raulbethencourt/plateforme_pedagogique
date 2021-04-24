<?php

namespace App\Repository;

use App\Entity\Questionnaire;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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
}

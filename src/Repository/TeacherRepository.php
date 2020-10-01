<?php

namespace App\Repository;

use App\Entity\Teacher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Teacher|null find($id, $lockMode = null, $lockVersion = null)
 * @method Teacher|null findOneBy(array $criteria, array $orderBy = null)
 * @method Teacher[]    findAll()
 * @method Teacher[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeacherRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Teacher::class);
    }

    /**
     * This method allows to find a teacher by a classroom
     * @param $clasroom
     * @return teacher[] Returns an array of teacher objects
     */
    public function findByClassroom($clasroom): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere(':val MEMBER OF f.classrooms')
            ->setParameter('val', $clasroom)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    /*public function findOneBySomeField($username): ?teacher
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.username = :val')
            ->setParameter('val', $username)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }*/

}

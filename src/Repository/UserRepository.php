<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
    * @return User[] Returns an array of User objects
    */
    public function findByRole($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :val')
            ->setParameter('val', "%$value%")
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * This method find the links with search bar data.
     */
    public function findBySearch(?string $name, ?string $surname, ?string $email, ?string $phone): array
    {
        $query = $this->createQueryBuilder('l');

        if (isset($name)) {
            $query = $query->andWhere('l.name LIKE :name')
                ->setParameter('name', "%{$name}%")
            ;
        }

        if (isset($surname)) {
            $query = $query->andWhere('l.surname LIKE :surname')
                ->setParameter('surname', "%{$surname}%")
            ;
        }

        if (isset($email)) {
            $query = $query->andWhere('l.email LIKE :email')
                ->setParameter('email', "%{$email}%")
            ;
        }

        if (isset($phone)) {
            $query = $query->andWhere('l.telephone = :phone')
                ->setParameter('phone', $phone)
            ;
        }

        return $query->orderBy('l.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}

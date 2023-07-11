<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class AdminFixtures
 * This class creates fictitious Admin for tests
 * @package App\DataFixtures
 */
class AdminFixtures extends Fixture
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 5; $i++) {
            $admin = new User();
            $admin->setUsername($faker->userName);
            $admin->setEmail($faker->safeEmail);
            $admin->setName($faker->name);
            $admin->setSurname($faker->firstNameFemale);
            $admin->setRoles(["ROLE_ADMIN"]);
            $admin->setPassword(
                $this->hasher->hashPassword($admin, "admin")
            );
            $admin->setEntryDate(new \DateTime());

            $manager->persist($admin);
        }

        $manager->flush();
    }
}

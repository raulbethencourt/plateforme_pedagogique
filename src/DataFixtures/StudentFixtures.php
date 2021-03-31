<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Student;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class StudentFixtures
 * This class creates fictitious Student for tests
 * @package App\DataFixtures
 */
class StudentFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 30; $i++) {
            $student = new Student();
            $student->setUsername($faker->userName);
            $student->setEmail($faker->safeEmail);
            $student->setName($faker->name);
            $student->setSurname($faker->firstNameMale);
            $student->setRoles(["ROLE_STUDENT"]);
            $student->setPassword(
                $this->passwordEncoder->encodePassword($student, "student")
            );
            $student->setEntryDate(new \DateTime());
            $manager->persist($student);
        }

        $manager->flush();
    }
}

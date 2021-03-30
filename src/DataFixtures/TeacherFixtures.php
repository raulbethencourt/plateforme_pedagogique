<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Teacher;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class TeacherFixtures
 * This class creates fictitious Teacher for tests
 * @package App\DataFixtures
 */
class TeacherFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 50; $i++) {
            $teacher = new Teacher();
            $teacher->setUsername($faker->userName);
            $teacher->setEmail($faker->safeEmail);
            $teacher->setName($faker->name);
            $teacher->setSurname($faker->firstNameMale);
            $teacher->setRoles(["ROLE_TEACHER"]);
            $teacher->setPassword(
                $this->passwordEncoder->encodePassword($teacher, "teacher")
            );
            $teacher->setEntryDate(new \DateTime());

            $manager->persist($teacher);
        }

        $manager->flush();
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Student;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
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
        for ($i = 0; $i < 50; $i++) {
            $student = new Student();
            $student->setUsername('student');
            $student->setEmail('student@test.mail');
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

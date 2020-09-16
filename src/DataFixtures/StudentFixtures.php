<?php

namespace App\DataFixtures;

use App\Entity\Student;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class StudentFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $student = new Student();
        $student->setUsername('student');
        $student->setEmail('student@test.mail');
        $student->setPassword(
            $this->passwordEncoder->encodePassword($student, "student")
        );
        $student->setEntryDate(new \DateTime());
        $manager->persist($student);

        $manager->flush();
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Teacher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TeacherFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $teacher = new Teacher();
        $teacher->setUsername('raul');
        $teacher->setEmail('raul@test.mail');
        $teacher->setPassword(
            $this->passwordEncoder->encodePassword($teacher, "raul")
        );
        $teacher->setEntryDate(new \DateTime());

        $manager->persist($teacher);

        $manager->flush();
    }
}

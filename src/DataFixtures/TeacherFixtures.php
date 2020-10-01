<?php

namespace App\DataFixtures;

use App\Entity\Teacher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class TeacherFixtures
 * This class creates fictitious Teacher for tests
 * @package App\DataFixtures
 */
class TeacherFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $teacher = new Teacher();
        $teacher->setUsername('teacher');
        $teacher->setEmail('teacher@test.mail');
        $teacher->setPassword(
            $this->passwordEncoder->encodePassword($teacher, "teacher")
        );
        $teacher->setEntryDate(new \DateTime());

        $manager->persist($teacher);

        $manager->flush();
    }
}

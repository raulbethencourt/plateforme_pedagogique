<?php

namespace App\DataFixtures;

use App\Entity\Teacher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TeacherFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $teacher = new Teacher();
        $teacher->setUsername('Raul');
        $teacher->setEmail('raul@test.mail');
        $teacher->setPassword('teacher');
        $teacher->setEntryDate(new \DateTime());

        $manager->persist($teacher);

        $manager->flush();
    }
}

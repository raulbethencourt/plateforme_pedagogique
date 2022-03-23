<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Classroom;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ClassroomFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 5; $i++) {
            $classroom = new Classroom();
            $classroom->setName($faker->streetName);
            $classroom->setDiscipline($faker->domainName);
            $manager->persist($classroom);
        }
        $manager->flush();
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\School;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SchoolFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $school = new School();
        $school->setName('Contact');
        
        $manager->persist($school);

        $manager->flush();
    }
}

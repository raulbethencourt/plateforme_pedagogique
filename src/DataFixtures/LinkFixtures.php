<?php

namespace App\DataFixtures;

use App\Entity\Link;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class LinkFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 2; ++$i) {
            $link = new Link();
            $link->setName($faker->city);
            $link->setLink($faker->url);
            $link->setCreator($faker->firstName);
            $link->setCategory($faker->domainWord);
            $link->setVisibility(true);
            $link->setUsable(true);
            $link->setDescription($faker->text);
            $manager->persist($link);
        }
        $manager->flush();
        $manager->flush();
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager){
        $admin = new User();
        $admin->setUsername("Admin");
        $admin->setEmail("admin@admin.com");
        $admin->setRoles(["ROLE_ADMIN"]);
        $admin->setPassword(
           $this->passwordEncoder->encodePassword($admin, "secret_text")
        );
        $admin->setEntryDate(new \DateTime());

        $manager->persist($admin);

        $manager->flush();
    }
}

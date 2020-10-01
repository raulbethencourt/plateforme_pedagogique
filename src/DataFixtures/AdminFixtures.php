<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AdminFixtures
 * This class creates fictitious Admin for tests
 * @package App\DataFixtures
 */
class AdminFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager){
        $admin = new User();
        $admin->setUsername("admin");
        $admin->setEmail("admin@test.mail");
        $admin->setRoles(["ROLE_ADMIN"]);
        $admin->setPassword(
           $this->passwordEncoder->encodePassword($admin, "admin")
        );
        $admin->setEntryDate(new \DateTime());

        $manager->persist($admin);

        $manager->flush();
    }
}

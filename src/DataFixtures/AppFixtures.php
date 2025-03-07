<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {

    }

    public function load(ObjectManager $manager): void
    {
        $admin = $this->createAdmin();

        $manager->persist($admin);

        $manager->flush();
    }

    protected function createAdmin(): User
    {
        $admin = new User();

        $passwordHashed = $this->hasher->hashPassword($admin, 'Harone162021');

        $admin->setEmail('harone@email.fr');
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $admin->setPassword($passwordHashed);
        $admin->setFirstname('Harone');
        $admin->setLastname('Doe');

        return $admin;

    }
}

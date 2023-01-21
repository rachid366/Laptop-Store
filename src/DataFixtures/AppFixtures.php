<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $Passwordhasher;

    public function __construct(UserPasswordHasherInterface $Passwordhasher)
    {
        $this->Passwordhasher = $Passwordhasher;
    }
    public function load(ObjectManager $manager): void
    {
         $user = new User();
         $plainPassword = "1234";
         $hashedPassword = $this->Passwordhasher->hashPassword($user,$plainPassword);
            $user->setUsername('admin');
            $user->setPassword($hashedPassword);
            $user->setRoles(['ROLES_ADMIN']);
         $manager->persist($user);

        $manager->flush();
    }
}

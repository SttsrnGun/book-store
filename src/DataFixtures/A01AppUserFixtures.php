<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\AppUser;

class A01AppUserFixtures extends Fixture
{
    private $passwordHasher;
    
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        // dd(new \Datetime());
        $user = new AppUser();
        $user->setEmail('test@email.com');
        $user->setRoles($user->getRoles());
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            '00000000'
        ));
        $manager->persist($user);
        for ($i=0; $i < 5; $i++) { 
            $user = new AppUser();
            $user->setEmail($i.'test@email.com');
            $user->setRoles($user->getRoles());
            $user->setPassword($this->passwordHasher->hashPassword(
                $user,
                '00000000'
            ));
            $manager->persist($user);
        }

        $manager->flush();
    }
}

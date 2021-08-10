<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

class UserFixtures extends Fixture
{
    private $passwordHasher;
    
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        // $2y$13$56f7QcBgK2DKm4ntV6.MjO7byHEGVYf6VwNCxuT2Ympo9sxmpdIdi
        $user = new User();
        $user->setEmail('test@email.com');
        $user->setRoles($user->getRoles());
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            '00000000'
        ));
        $manager->persist($user);

        $manager->flush();
    }
}

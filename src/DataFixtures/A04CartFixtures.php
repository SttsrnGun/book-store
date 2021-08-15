<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Book;
use App\Entity\User;
use App\Entity\Cart;

class A04CartFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $users = $manager->getRepository(User::class)->findAll();
        $books = $manager->getRepository(Book::class)->findAll();

        foreach ($users as $user) {
            foreach ($books as $book) {
                if(rand(1,5)){
                    $cart = new Cart();
                    $cart->setUser($user);
                    $cart->setBook($book);
                    $cart->setAmount(rand(1,20));
                    $manager->persist($cart);
                }
            }
        }
        $manager->flush();
    }
}

<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Book;

class BookFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        
        for ($i=0; $i < 20; $i++) { 
            $book = new Book();
            $book->setName('Book'.$i);
            $book->setDetail(
                $i.
                "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum."
                .$i
            );
            $book->setPrice($i+20);
            $manager->persist($book);
        }
        
        $manager->flush();
    }
}

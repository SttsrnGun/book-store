<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\ReviewBook;
use App\Entity\Book;

class ReviewBookFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        
        $books = $manager->getRepository(Book::class)->findAll();
        
        foreach ($books as $book) {
            for ($i=0; $i < rand(20,40); $i++) { 
                $reviewBook = new ReviewBook();
                $reviewBook->setBook($book);
                $reviewBook->setScore(rand(1,5));
                $manager->persist($reviewBook);
            }
        }
        $manager->flush();
    }
}

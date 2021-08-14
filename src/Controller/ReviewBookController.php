<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\ReviewBook;
use App\Entity\Book;
use Symfony\Component\HttpFoundation\JsonResponse;

class ReviewBookController extends AbstractController
{
    /**
     * @Route("/review/book", name="review_book")
     */
    // public function index(Request $request,EntityManagerInterface $entityManager): Response
    // {
        
    //     $bookId = $request->query->get('book_id');
        
    //     $reviewBookList = $entityManager->getRepository('App\Entity\ReviewBook')->findBy(['book'=>$bookId]);
    //     $cScore = 0;
    //     foreach ($reviewBookList as $reviewBook) {
    //         $cScore=$cScore+$reviewBook->getScore();
    //     }

    //     $avgScore = $cScore/count($reviewBookList);

    //     return new JsonResponse($avgScore, 200);
    // }
}

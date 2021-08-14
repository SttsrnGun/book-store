<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class BookController extends AbstractController
{
    /**
     * @Route("/book", name="book")
     */
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }
    /**
     * @Route("/api/search/book", name="search book by name, detail, about")
     */
    public function searchBook(Request $request,EntityManagerInterface $entityManager): Response{
        $keyword = $request->query->get('keyword');
        $bookList = $entityManager->getRepository('App\Entity\Book')
            ->findByKeyword($keyword,10);
            
        return new JsonResponse($bookList, 200);
    }
}

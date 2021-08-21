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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Entity\AppUser;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ReviewBookController extends AbstractController
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

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
    /**
     * @Route("api/book/review", name="review book", methods={"POST"} )
     */
    public function reviewBook(Request $request, EntityManagerInterface $entityManager): Response
    {
        $token = $this->tokenStorage->getToken();
        $user = $token->getUser();


        $payload = json_decode($request->getContent(), true);
        $bookId = $payload['bookId'];
        $score = $payload['score'];

        try {
            if (!$bookId) {
                throw new BadRequestHttpException('bookId is required');
            }

            if (!in_array($score, [1, 2, 3, 4, 5])) {
                throw new BadRequestHttpException('score is invalid');
            }
            $book = $entityManager
                ->getRepository('App\Entity\Book')
                ->find($bookId);

            if ($book->getOwner() && $book->getOwner()->getId() == $user->getId()) {
                throw new BadRequestHttpException('You are owner the book.');
            }

            if (!$book) {
                throw new BadRequestHttpException('bookId is invalid');
            }

            $reviewBookList = $entityManager
                ->getRepository('App\Entity\ReviewBook')
                ->findBy([
                    'book' => $bookId,
                    'user' => $user->getId()
                ]);
            if ($reviewBookList) {
                throw new BadRequestHttpException('You are already reviewed.');
            }
            $reviewObj = new ReviewBook();
            $reviewObj->setUser($user);
            $reviewObj->setScore($score);
            $reviewObj->setBook($book);
            $entityManager->persist($reviewObj);
            $entityManager->flush();
        } catch (\Throwable $th) {
            return new JsonResponse($th->getMessage(), 400);
        }
        return new JsonResponse('Success', 200);
    }
}

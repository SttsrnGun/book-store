<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Enum\BookTag;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Entity\AppUser;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class BookController extends AbstractController
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
     * @Route("/book", name="book")
     */
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }
    /**
     * @Route("/api/search/book", name="search book by name, detail, about"  )
     */
    public function searchBook(Request $request, EntityManagerInterface $entityManager): Response
    {
        try {
            $keyword = $request->query->get('keyword');
            $bookList = $entityManager->getRepository('App\Entity\Book')
                ->findByKeyword($keyword, 10);
        } catch (\Throwable $th) {
            return new JsonResponse($th->getMessage(), 400);
        }
        return new JsonResponse($bookList, 200);
    }

    /**
     * @Route("api/book/add", name="add Cart auto fill some field", methods={"POST"} )
     */
    public function addBook(Request $request, EntityManagerInterface $entityManager): Response
    {
        $token = $this->tokenStorage->getToken();
        $user = $token->getUser();

        $payload = json_decode($request->getContent(), true);
        $title = $payload['title'];
        $summary = $payload['summary'];
        $tag = $payload['tag'];
        $price = $payload['price'];
        $author = $payload['author'];
        $image = $payload['image'];
        $isHidden = $payload['isHidden'];
        $discount = $payload['discount'];

        if (!$image) {
            $image = 'https://media.istockphoto.com/vectors/no-image-available-sign-vector-id936182806?k=6&m=936182806&s=612x612&w=0&h=F5sh9tAuiAtEPNE1NiFZ7mH7-7cjx0q4CXOcxiziFpw=';
        }
        if (!$isHidden) {
            $isHidden = false;
        }

        try {
            if (!$title || !$summary || !$tag || !$price || !$author) {
                throw new BadRequestHttpException('Invalid paramters.');
            }
            if ($discount > $price) {
                throw new BadRequestHttpException('Discount must be greater than price.');
            }
            if (!array_search($tag, BookTag::toArray())) {
                throw new BadRequestHttpException('Tag must be "RECOMENDED","BEST_SELLERS"');
            }
            $book = new Book();
            $book->setName($title);
            $book->setDetail($summary);
            $book->setTag([BookTag::toArray()[array_search($tag, BookTag::toArray())]]);
            $book->setPrice($price);
            $book->setAbout($author);
            $book->setImagePath($image);
            $book->setIsHidden($isHidden);
            $book->setOwner($user);
            if ($discount) {
                $book->setDiscount($discount);
            }
            $book->setCreateAt(new \DateTimeImmutable());
            $entityManager->persist($book);
            $entityManager->flush();
        } catch (\Throwable $th) {
            return new JsonResponse($th->getMessage(), 400);
        }
        return new JsonResponse('Success', 200);
    }

    /**
     * @Route("api/book/update", name="update owner book", methods={"PATCH"} )
     */
    public function updateBook(Request $request, EntityManagerInterface $entityManager): Response
    {
        $token = $this->tokenStorage->getToken();
        $user = $token->getUser();

        $payload = json_decode($request->getContent(), true);
        $bookId = $payload['bookId'];
        $title = $payload['title'];
        $summary = $payload['summary'];
        $tag = $payload['tag'];
        $price = $payload['price'];
        $author = $payload['author'];
        $image = $payload['image'];
        $isHidden = $payload['isHidden'];
        $discount = $payload['discount'];

        try {
            if (!$bookId) {
                throw new BadRequestHttpException('bookId is invalid');
            }
            if ($discount > $price) {
                throw new BadRequestHttpException('Discount must be greater than price.');
            }
            if (!array_search($tag, BookTag::toArray())) {
                throw new BadRequestHttpException('Tag must be "RECOMENDED","BEST_SELLERS"');
            }
            $book = $entityManager
                ->getRepository('App\Entity\Book')
                ->findBy([
                    'owner' => $user->getId(),
                    'id' => $bookId
                ])[0];
            if ($title) {
                $book->setName($title);
            }
            if ($summary) {
                $book->setDetail($summary);
            }
            if ($tag) {
                $book->setTag([BookTag::toArray()[array_search($tag, BookTag::toArray())]]);
            }
            if ($price) {
                $book->setPrice($price);
            }
            if ($author) {
                $book->setAbout($author);
            }
            if ($image) {
                $book->setImagePath($image);
            }
            if ($isHidden) {
                $book->setIsHidden($isHidden);
            }
            if ($discount) {
                $book->setDiscount($discount);
            }
            $entityManager->persist($book);
            $entityManager->flush();
        } catch (\Throwable $th) {
            return new JsonResponse($th->getMessage(), 400);
        }
        return new JsonResponse('Success', 200);
    }

    /**
     * @Route("api/book/delete", name="soft delete user\'s book", methods={"DELETE"} )
     */
    public function deleteBook(Request $request, EntityManagerInterface $entityManager): Response
    {
        $token = $this->tokenStorage->getToken();
        $user = $token->getUser();

        $payload = json_decode($request->getContent(), true);
        $bookId = $payload['bookId'];

        try {
            $book = $entityManager
                ->getRepository('App\Entity\Book')
                ->find($bookId);
            if (!$book) {
                throw new BadRequestHttpException('bookId is invalid.');
            }

            if (!$book->getDeletedAt()) {
                throw new BadRequestHttpException('book already deleted.');
            }

            if ($book->getOwner() && ($book->getOwner()->getId() == $user->getId())) {
                $book->setDeletedAt(new \DateTimeImmutable());
                $book->setIsHidden(true);
                $entityManager->persist($book);
                $entityManager->flush();
            }
        } catch (\Throwable $th) {
            return new JsonResponse($th->getMessage(), 400);
        }
        return new JsonResponse('Success', 200);
    }
}

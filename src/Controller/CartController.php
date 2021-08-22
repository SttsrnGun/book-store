<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Book;
use App\Entity\AppUser;
use App\Entity\Cart;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use DateTimeInterface;

class CartController extends AbstractController
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
     * @Route("/cart", name="cart")
     */
    public function index(): Response
    {
        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
        ]);
    }

    /**
     * @Route("/api/cart/add", name="addCart", methods={"POST"})
     */
    public function addCart(Request $request, EntityManagerInterface $entityManager): Response
    {
        $token = $this->tokenStorage->getToken();
        $user = $token->getUser();

        $payload = json_decode($request->getContent(), true);
        $bookId = $payload['bookId'];
        $amount = $payload['amount'];

        $cartArray = $entityManager->getRepository(Cart::class)->findByUserBook($user->getId(), $bookId);
        if ($cartArray) { //update
            $cartObj = $entityManager->getRepository(Cart::class)->find($cartArray[0]['id']);
            if ($amount == 0) { //delete
                $cartObj->setDeletedAt(new \DateTime());
            } else {
                $cartObj->setAmount($amount);
            }
            $entityManager->persist($cartObj);
            $entityManager->flush();
        } else { //insert
            $book = $entityManager->getRepository(Book::class)->find($bookId);
            $cart = new Cart();
            $cart->setUser($user);
            $cart->setBook($book);
            $cart->setAmount($amount);
            $entityManager->persist($cart);
            $entityManager->flush();
        }

        return new JsonResponse($payload, 200);
    }

    /**
     * @Route("/api/carts/me", name="getCartMe")
     */
    public function getCartMe(Request $request, EntityManagerInterface $entityManager): Response
    {
        try {
            $bookId = $request->query->get('bookId');
            $token = $this->tokenStorage->getToken();
            $user = $token->getUser();

            $cart = $entityManager->getRepository(Cart::class)->findByUserBook($user->getId(), $bookId);
        } catch (\Throwable $th) {
            return new JsonResponse($th->getMessage(), 400);
        }
        return new JsonResponse($cart, 200);
    }

    /**
     * @Route("/api/cart/clear", name="getCartClear", methods={"POST"})
     */
    public function getCartClear(EntityManagerInterface $entityManager): Response
    {
        $token = $this->tokenStorage->getToken();
        $user = $token->getUser();
        try {
            $cartArray = $entityManager->getRepository(Cart::class)->findByUserBook($user->getId());
            foreach ($cartArray as $cart) {
                $cartObj = $entityManager->getRepository(Cart::class)->find($cart['id']);
                $cartObj->setDeletedAt(new \DateTime());
                $entityManager->persist($cartObj);
            }
            $entityManager->flush();
        } catch (\Throwable $th) {
            return new JsonResponse($th->getMessage(), 400);
        }
        return new JsonResponse($cart, 200);
    }

    /**
     * @Route("/api/carts/summary", name="getCartSummary")
     */
    public function getCartSummary(Request $request, EntityManagerInterface $entityManager): Response
    {
        $token = $this->tokenStorage->getToken();
        $user = $token->getUser();
        $sumPrice = 0;
        $deliver = 20;
        $netPrice = 0;
        try {
            $cart = $entityManager->getRepository(Cart::class)->findByUserBook($user->getId());
            foreach ($cart as $item) {
                $price = $item['book']['price'];
                $discount = $item['book']['discount'];
                $amount = $item['amount'];
                if (!$discount) {
                    $discount = 0;
                }
                $sumPrice += ($price - $discount) * $amount;
            }
            $netPrice = $sumPrice - $deliver;
        } catch (\Throwable $th) {
            return new JsonResponse($th->getMessage(), 400);
        }
        return new JsonResponse([
            'sumPrice' => $sumPrice,
            'deliver' => $deliver,
            'netPrice' => $netPrice
        ], 200);
    }
}

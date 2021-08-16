<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
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
     * @Route("/user", name="user")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    /**
     * @Route("/users/mef", name="userMe")
     */
    // public function getUserMe(): ?User
    // {
    //     $token = $this->tokenStorage->getToken();

    //     if (!$token) {
    //         return null;
    //     }

    //     $user = $token->getUser();

    //     if (!$user instanceof User) {
    //         return null;
    //     }

    //     return $user;
    // }

    /**
     * @Route("/api/users/register", name="userRegister")
     */
    public function getUserRegister(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $payload = json_decode($request->getContent(), true);
        $email = $payload['email'];
        $password = $payload['password'];

        try {
            $user = new User();
            $user->setEmail($email);
            $user->setRoles($user->getRoles());
            $user->setPassword($passwordHasher->hashPassword(
                $user,
                $password
            ));
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (\Throwable $th) {
            return new JsonResponse($th->getMessage(), 400);
        }
        return new JsonResponse($payload, 200);
    }
}

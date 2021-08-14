<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Enum\BookTag;
use Symfony\Component\HttpFoundation\JsonResponse;

class EnumController extends AbstractController
{
    /**
     * @Route("/enum", name="enum")
     */
    public function index(): Response
    {
        return $this->render('enum/index.html.twig', [
            'controller_name' => 'EnumController',
        ]);
    }
    /**
     * @Route(
     *     name="enums",
     *     path="/api/enums",
     *     methods={"GET"}
     * )
     */
    public function getEnum(): Response
    {
        $enums = [
            'bookTag' => BookTag::toArray(),
        ];
        return new JsonResponse($enums, 200);
    }
}

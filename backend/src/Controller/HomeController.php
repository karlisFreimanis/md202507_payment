<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route(
        path: '/',
        name: 'home',
        methods: ['GET'],
    )]
    public function index(): JsonResponse
    {
        return $this->json(
            [
                'Template works',
            ],
        );
    }
}
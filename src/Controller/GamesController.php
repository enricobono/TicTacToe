<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class GamesController extends AbstractController
{

    #[Route('/random', name: 'random-index', format: 'json')]
    public function number(): JsonResponse
    {
        $number = random_int(0, 100);

        return $this->json([
            'number'   => $number
        ]);
    }
}

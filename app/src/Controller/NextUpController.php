<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class NextUpController extends AbstractController
{
    public function search():JsonResponse
    {
        return new JsonResponse([
            'message' => 'Hello World!',
            'status' => 200,
            'title' => 'OK'
        ]);
    }
}
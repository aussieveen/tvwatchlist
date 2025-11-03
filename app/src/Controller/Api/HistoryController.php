<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Document\History;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/histories', name: 'api_histories_')]
final class HistoryController extends BaseEntityController
{
    #[Route(name: 'post', methods: ['POST'])]
    public function post(Request $request): JsonResponse
    {
        return parent::create($request);
    }

    protected function getEntityClass(): string
    {
        return History::class;
    }

    protected function getWriteGroup(): string
    {
        return History::WRITE_GROUP;
    }
}

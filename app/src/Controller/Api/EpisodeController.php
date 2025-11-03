<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Document\Episode;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/episodes', name: 'api_episodes_')]
final class EpisodeController extends BaseEntityController
{
    #[Route('/{id}', name: 'patch', methods: ['PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        return parent::update($id, $request);
    }

    protected function getEntityClass(): string
    {
        return Episode::class;
    }

    protected function getWriteGroup(): string
    {
        return Episode::WRITE_GROUP;
    }
}

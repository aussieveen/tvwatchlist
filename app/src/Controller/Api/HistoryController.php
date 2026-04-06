<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Document\History as HistoryDocument;
use Doctrine\ODM\MongoDB\DocumentManager;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[OA\Tag(name: 'History')]
class HistoryController extends AbstractController
{
    public function __construct(
        private readonly DocumentManager $dm,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    ) {
    }

    #[Route('/api/histories', name: 'history_collection', methods: ['GET'])]
    #[OA\Get(summary: 'List watch history', responses: [
        new OA\Response(response: 200, description: 'OK'),
    ])]
    public function collection(): JsonResponse
    {
        $histories = $this->dm->createQueryBuilder(HistoryDocument::class)
            ->sort('id', 'DESC')
            ->getQuery()
            ->execute()
            ->toArray();

        return $this->json(
            array_values($histories),
            context: ['groups' => ['history:read'], 'skip_null_values' => true]
        );
    }

    #[Route('/api/histories/{id}', name: 'history_get', methods: ['GET'])]
    #[OA\Get(summary: 'Get a history entry', responses: [
        new OA\Response(response: 200, description: 'OK'),
        new OA\Response(response: 404, description: 'Not found'),
    ])]
    public function get(int $id): JsonResponse
    {
        $history = $this->dm->find(HistoryDocument::class, $id);

        if (!$history) {
            return $this->json(['error' => 'History entry not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($history, context: ['groups' => ['history:read'], 'skip_null_values' => true]);
    }

    #[Route('/api/histories', name: 'history_post', methods: ['POST'])]
    #[OA\Post(summary: 'Create a history entry', responses: [
        new OA\Response(response: 201, description: 'Created'),
        new OA\Response(response: 400, description: 'Validation error'),
    ])]
    public function post(Request $request): JsonResponse
    {
        $history = $this->serializer->deserialize(
            $request->getContent(),
            HistoryDocument::class,
            'json',
            ['groups' => ['history:write']]
        );

        $errors = $this->validator->validate($history);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->dm->persist($history);
        $this->dm->flush();

        return $this->json(
            $history,
            Response::HTTP_CREATED,
            context: ['groups' => ['history:read'], 'skip_null_values' => true]
        );
    }

    #[Route('/api/histories/{id}', name: 'history_delete', methods: ['DELETE'])]
    #[OA\Delete(summary: 'Delete a history entry', responses: [
        new OA\Response(response: 204, description: 'Deleted'),
        new OA\Response(response: 404, description: 'Not found'),
    ])]
    public function delete(int $id): JsonResponse
    {
        $history = $this->dm->find(HistoryDocument::class, $id);

        if (!$history) {
            return $this->json(['error' => 'History entry not found'], Response::HTTP_NOT_FOUND);
        }

        $this->dm->remove($history);
        $this->dm->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}

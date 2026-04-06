<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Document\Episode as EpisodeDocument;
use Doctrine\ODM\MongoDB\DocumentManager;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[OA\Tag(name: 'Episodes')]
class EpisodeController extends AbstractController
{
    public function __construct(
        private readonly DocumentManager $dm,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    ) {
    }

    #[Route('/api/episodes', name: 'episode_collection', methods: ['GET'])]
    #[OA\Get(summary: 'List all episodes', responses: [
        new OA\Response(response: 200, description: 'OK'),
    ])]
    public function collection(): JsonResponse
    {
        $episodes = $this->dm->createQueryBuilder(EpisodeDocument::class)
            ->sort('airDate', 'ASC')
            ->getQuery()
            ->execute()
            ->toArray();

        return $this->json(
            array_values($episodes),
            context: ['groups' => ['episode:read'], 'skip_null_values' => true]
        );
    }

    #[Route('/api/episodes/{id}', name: 'episode_get', methods: ['GET'])]
    #[OA\Get(summary: 'Get an episode', responses: [
        new OA\Response(response: 200, description: 'OK'),
        new OA\Response(response: 404, description: 'Not found'),
    ])]
    public function get(int $id): JsonResponse
    {
        $episode = $this->dm->find(EpisodeDocument::class, $id);

        if (!$episode) {
            return $this->json(['error' => 'Episode not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($episode, context: ['groups' => ['episode:read'], 'skip_null_values' => true]);
    }

    #[Route('/api/episodes/{id}', name: 'episode_patch', methods: ['PATCH'])]
    #[OA\Patch(summary: 'Update an episode', responses: [
        new OA\Response(response: 200, description: 'OK'),
        new OA\Response(response: 400, description: 'Validation error'),
        new OA\Response(response: 404, description: 'Not found'),
    ])]
    public function patch(int $id, Request $request): JsonResponse
    {
        $episode = $this->dm->find(EpisodeDocument::class, $id);

        if (!$episode) {
            return $this->json(['error' => 'Episode not found'], Response::HTTP_NOT_FOUND);
        }

        $this->serializer->deserialize(
            $request->getContent(),
            EpisodeDocument::class,
            'json',
            [
                AbstractNormalizer::OBJECT_TO_POPULATE => $episode,
                'groups' => ['episode:write'],
            ]
        );

        $errors = $this->validator->validate($episode);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->dm->flush();

        return $this->json($episode, context: ['groups' => ['episode:read'], 'skip_null_values' => true]);
    }

    #[Route('/api/episodes/{id}', name: 'episode_delete', methods: ['DELETE'])]
    #[OA\Delete(summary: 'Delete an episode', responses: [
        new OA\Response(response: 204, description: 'Deleted'),
        new OA\Response(response: 404, description: 'Not found'),
    ])]
    public function delete(int $id): JsonResponse
    {
        $episode = $this->dm->find(EpisodeDocument::class, $id);

        if (!$episode) {
            return $this->json(['error' => 'Episode not found'], Response::HTTP_NOT_FOUND);
        }

        $this->dm->remove($episode);
        $this->dm->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}

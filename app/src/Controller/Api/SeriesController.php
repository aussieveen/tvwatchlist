<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Document\Episode;
use App\Repository\EpisodeRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SeriesController extends AbstractController
{
    private EpisodeRepository $episodeRepository;

    public function __construct(
        DocumentManager $documentManager
    ) {
        $this->episodeRepository = $documentManager->getRepository(Episode::class);
    }

    #[Route('/api/series/{tvdbSeriesId}', name: 'remove_series', methods: ['DELETE'])]
    public function removeSeries(string $tvdbSeriesId): JsonResponse
    {
        $this->episodeRepository->deleteEpisodesWithTvdbSeriesId($tvdbSeriesId);
        return new JsonResponse('', Response::HTTP_NO_CONTENT);
    }
}

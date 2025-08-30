<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Document\Episode;
use App\Document\History;
use App\Helper\NextUpHelper;
use App\Repository\EpisodeRepository;
use DateTimeImmutable;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EpisodeController extends AbstractController
{
    private EpisodeRepository $episodeRepository;

    public function __construct(
        private readonly NextUpHelper $nextUpEpisodeHelper,
        private DocumentManager $documentManager
    ) {
        $this->episodeRepository = $this->documentManager->getRepository(Episode::class);
        $this->historyRepository = $this->documentManager->getRepository(History::class);
    }

    #[Route(path: '/api/episode/nextup', name: 'next_up')]
    public function nextUp(): JsonResponse
    {
        $seriesTitle = $this->nextUpEpisodeHelper->getSeriesNotOnRecentlyWatchedList();

        if (!$seriesTitle) {
            $seriesTitle = $this->nextUpEpisodeHelper->getSeriesFromRecentlyWatchedList();
        }

        return $seriesTitle
            ? $this->json($this->episodeRepository->getLatestUnwatchedFromSeries($seriesTitle) ?? [])
            : $this->json([]);
    }

    #[Route(path: '/api/episode/{episodeId}/watched', name: 'watched', methods: 'POST')]
    public function watched(int $episodeId): JsonResponse
    {
        $episode = $this->episodeRepository->find($episodeId);

        if ($episode) {
            $episode->watched = true;
            $this->documentManager->flush();
        }

        $history = new History();
        $history->seriesTitle = $episode->seriesTitle;
        $history->episodeTitle = $episode->title;
        $history->airDate = $episode->airDate;
        $history->universe = $episode->universe ?? null;
        $history->watchedAt = new DateTimeImmutable();

        $this->documentManager->persist($history);
        $this->documentManager->flush();

        return new JsonResponse('', Response::HTTP_NO_CONTENT);
    }
}

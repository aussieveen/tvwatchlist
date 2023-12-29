<?php

declare(strict_types=1);

namespace App\Controller;

use App\Adapter\QueryAdapter;
use App\Entity\Search\ApiQuery;
use App\Helper\EpisodeSelector;
use App\Repository\Episode;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NextUpController extends AbstractController
{
    public function __construct(
        private readonly EpisodeSelector $episodeSelector,
        private readonly Episode $episode
    ) {
    }

    #[Route('/api/nextup', name: 'next_up')]
    public function search():Response
    {
        $showTitle = $this->episodeSelector->getShowNotOnRecentlyWatchedList();
        if (!$showTitle) {
            $showTitle = $this->episodeSelector->getShowFromRecentlyWatchedList();
        }

        $episode = $this->episode->getLatestUnwatchedEpisode($showTitle);

        return $this->json($episode ?? []);
    }
}

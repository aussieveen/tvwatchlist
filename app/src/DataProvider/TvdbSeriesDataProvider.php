<?php

declare(strict_types=1);

namespace App\DataProvider;

use App\Api\TvdbQueryClient;
use App\Document\Episode as EpisodeDocument;
use App\Entity\Tvdb\Episode;
use App\Entity\Tvdb\Series;
use App\Processor\TvdbEpisodeData;

class TvdbSeriesDataProvider
{
    private const REGULAR_SEASON_TYPE = 1;

    public function __construct(
        private TvdbQueryClient $client,
        private TvdbEpisodeData $episodeDataProcessor
    ) {
    }

    public function getSeries(
        string $tvdbSeriesId,
        int $fromSeason = 1,
        int $fromEpisode = 1
    ): ?Series {
        $tvdbSeriesData = json_decode($this->client->seriesExtended($tvdbSeriesId)->getContent(), true);
        if ($tvdbSeriesData['status'] !== 'success') {
            return null;
        }

        $series = new Series(
            $tvdbSeriesId,
            $tvdbSeriesData['data']['name'],
            $tvdbSeriesData['data']['image'],
            $tvdbSeriesData['data']['status']['id']
        );

        foreach ($tvdbSeriesData['data']['seasons'] as $seasonData) {
            if (
                $seasonData['type']['id'] !== self::REGULAR_SEASON_TYPE
                || $seasonData['number'] < $fromSeason
            ) {
                continue;
            }

            $seasonResponse = $this->client->seasonExtended((string) $seasonData['id']);

            $season = json_decode($seasonResponse->getContent(), true);

            if ($season['status'] !== 'success') {
                return null;
            }

            $this->episodeDataProcessor->addEpisodeDataToSeries(
                $series,
                $season['data']['episodes'],
                $seasonData['number'] === $fromSeason ? $fromEpisode : 1
            );
        }

        return $series;
    }
}

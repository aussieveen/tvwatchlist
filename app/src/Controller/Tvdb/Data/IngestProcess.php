<?php

declare(strict_types=1);

namespace App\Controller\Tvdb\Data;

use App\Api\TvdbQueryClient;
use App\Document\Episode;
use App\Entity\Tvdb\Data\Ingest\Criteria;
use App\Factory\EpisodeDocumentFactory;
use DateTimeImmutable;
use Doctrine\ODM\MongoDB\DocumentManager;
use Exception;
use RuntimeException;

readonly class IngestProcess
{
    private const REGULAR_SEASON_TYPE = 1;

    public function __construct(
        private TvdbQueryClient $tvdbClient,
        private EpisodeDocumentFactory $episodeFactory,
        private DocumentManager $documentManager
    )
    {
    }

    public function ingest(Criteria $criteria): void
    {
        $seriesResponse = $this->tvdbClient->seriesExtended($criteria->seriesId);

        $series = json_decode($seriesResponse->getContent(), true);
        if ($series['status'] !== 'success') {
            throw new RuntimeException('Series not found');
        }

        $episodeRepository = $this->documentManager->getRepository(Episode::class);

        try {
            foreach ($series['data']['seasons'] as $seasonData) {
                // Don't import specials or seasons before the one we're looking for
                if (
                    $seasonData['type']['id'] !== self::REGULAR_SEASON_TYPE
                    || $seasonData['number'] < $criteria->season
                ) {
                    continue;
                }

                $seasonResponse = $this->tvdbClient->seasonExtended((string) $seasonData['id']);

                $season = json_decode($seasonResponse->getContent(), true);

                if ($season['status'] !== 'success') {
                    throw new RuntimeException('Episodes not found');
                }

                foreach ($season['data']['episodes'] as $episodeData) {
                    // Don't import episodes before the one we're looking for
                    if (
                        $episodeData['seasonNumber'] === $criteria->season
                        && $episodeData['number'] < $criteria->episode
                    ) {
                        continue;
                    }

                    // Find and update existing episode if it was previously ingested. Otherwise, create a new one.
                    $existingEpisode = $episodeRepository->findOneBy([
                        'tvdbEpisodeId' => $episodeData['id'],
                    ]);

                    $episode = $this->episodeFactory->build(
                        $episodeData['name'],
                        (string) $episodeData['id'],
                        $episodeData['overview'] ?? '',
                        $episodeData['seasonNumber'],
                        $episodeData['number'],
                        $series['data']['name'],
                        $criteria->seriesId,
                        $series['data']['image'],
                        $criteria->platform,
                        Episode::VALID_STATUSES[$series['data']['status']['id']],
                        isset($episodeData['aired']) ? new DateTimeImmutable($episodeData['aired']) : null,
                        $criteria->universe,
                        $existingEpisode
                    );

                    $this->documentManager->persist($episode);
                    $this->documentManager->flush();
                }
            }
        } catch (Exception $e) {
        }
    }
}

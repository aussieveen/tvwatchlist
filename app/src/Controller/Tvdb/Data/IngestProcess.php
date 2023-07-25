<?php

namespace App\Controller\Tvdb\Data;

use App\Api\TvdbClient;
use App\Document\Episode;
use App\Document\EpisodeFactory;
use App\Entity\Tvdb\Data\Ingest\Criteria;
use DateTimeImmutable;
use Doctrine\ODM\MongoDB\DocumentManager;
use Exception;
use RuntimeException;

readonly class IngestProcess
{
    public function __construct(
        private TvdbClient $tvdbClient,
        private EpisodeFactory $episodeFactory,
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

        try{
            foreach ($series['data']['seasons'] as $seasonData) {
                if ($seasonData['type']['id'] !== 1 || $seasonData['number'] < $criteria->season) {
                    continue;
                }

                $seasonResponse = $this->tvdbClient->seasonExtended($seasonData['id']);

                $season = json_decode($seasonResponse->getContent(), true);

                if ($season['status'] !== 'success') {
                    throw new RuntimeException('Episodes not found');
                }

                foreach ($season['data']['episodes'] as $episodeData) {
                    if ($episodeData['seasonNumber'] === $criteria->season && $episodeData['number'] < $criteria->episode) {
                        continue;
                    }

                    $episode = $this->episodeFactory->build(
                        $episodeData['name'],
                        $episodeData['overview'],
                        $episodeData['seasonNumber'],
                        $episodeData['number'],
                        $series['data']['name'],
                        $series['data']['image'],
                        $criteria->platform,
                        Episode::VALID_STATUSES[$series['data']['status']['id']],
                        new DateTimeImmutable($episodeData['aired'])
                    );
                    $this->documentManager->persist($episode);
                    $this->documentManager->flush();
                }
            }
        }catch(Exception $e){
        }

    }
}

<?php

declare(strict_types=1);

namespace App\Repository;

use App\Document\Episode;
use App\Document\Episode as EpisodeDocument;
use App\Document\History;
use DateTimeInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\BSON\UTCDateTime;

class Series
{
    public function __construct(
        private readonly DocumentManager $documentManager
    ) {
    }

    public function getTitlesRecentlyWatched(): array
    {
        $builder = $this->documentManager->createAggregationBuilder(History::class);
        $builder->sort('id', 'DESC')->limit(5);

        $watched = [];
        foreach ($builder->getAggregation()->getIterator()->toArray() as $result) {
            $watched[] = $result['seriesTitle'];
        }
        return $watched;
    }

    public function getTitlesWithWatchableEpisodes(): array
    {
        $builder = $this->documentManager->createAggregationBuilder(Episode::class);
        $builder->match()->field('watched')->equals(false)
            ->group()->field('id')->expression('$seriesTitle');

        $seriesList = [];
        foreach ($builder->getAggregation()->getIterator()->toArray() as $series) {
            $seriesList[] = $series['_id'];
        }

        return $seriesList;
    }

    public function getTitlesNotRecentlyWatchedAndNotInAnUniverse(): array
    {
        $builder = $this->documentManager->createAggregationBuilder(Episode::class);
        $builder->match()->field('watched')->equals(false)
            ->match()->field('seriesTitle')->notIn($this->getTitlesRecentlyWatched())
            ->match()->field('universe')->equals('')
            ->group()->field('id')->expression('$seriesTitle');

        $seriesList = [];
        foreach ($builder->getAggregation()->getIterator()->toArray() as $series) {
            $seriesList[] = $series['_id'];
        }

        return $seriesList;
    }

    public function getUniverses(): array
    {
        $builder = $this->documentManager->createAggregationBuilder(Episode::class);
        $builder->match()->field('watched')->equals(false)
            ->match()->field('universe')->notEqual('')
            ->group()->field('id')->expression('$universe');

        $universeList = [];
        foreach ($builder->getAggregation()->getIterator()->toArray() as $universe) {
            $universeList[] = $universe['_id'];
        }

        return $universeList;
    }

    public function getLatestTitleFromUniverse(string $universe): string
    {
        $builder = $this->documentManager->createAggregationBuilder(EpisodeDocument::class);
        $builder->match()->field('watched')->equals(false)
            ->match()->field('universe')->equals($universe)
            ->sort('airDate', 'ASC')
            ->limit(1);

        return $builder->getAggregation()->getIterator()->toArray()[0]['seriesTitle'] ?? '';
    }

    public function getUnfinishedSeriesTitles(): array
    {
        $builder = $this->documentManager->createAggregationBuilder(EpisodeDocument::class);
        $builder->match()->field('status')->notEqual(EpisodeDocument::VALID_STATUSES[EpisodeDocument::STATUS_FINISHED])
            ->group()->field('id')->expression('$seriesTitle');

        $seriesList = [];
        foreach ($builder->getAggregation()->getIterator()->toArray() as $series) {
            $seriesList[] = $series['_id'];
        }
        return $seriesList;
    }

    public function getSeriesTitlesWithAvailableCurrentSeason(DateTimeInterface $now): array
    {
        // Get the min unwatched season per series
        $builder = $this->documentManager->createAggregationBuilder(EpisodeDocument::class);
        $builder->match()->field('watched')->equals(false)
            ->group()
            ->field('id')->expression('$seriesTitle')
            ->field('minSeason')->min('$season');

        $seriesMinSeasons = [];
        foreach ($builder->getAggregation()->getIterator()->toArray() as $result) {
            $seriesMinSeasons[$result['_id']] = $result['minSeason'];
        }

        if (empty($seriesMinSeasons)) {
            return [];
        }

        // Get the max airDate per (seriesTitle, season) across all episodes
        $airDateBuilder = $this->documentManager->createAggregationBuilder(EpisodeDocument::class);
        $airDateBuilder->group()
            ->field('id')->expression(['seriesTitle' => '$seriesTitle', 'season' => '$season'])
            ->field('maxAirDate')->max('$airDate');

        $seasonMaxAirDates = [];
        foreach ($airDateBuilder->getAggregation()->getIterator()->toArray() as $result) {
            $airDate = $result['maxAirDate'] ?? null;
            if ($airDate instanceof UTCDateTime) {
                $airDate = $airDate->toDateTime();
            }
            $seasonMaxAirDates[$result['_id']['seriesTitle'] . '::' . $result['_id']['season']] = $airDate;
        }

        // Return series where the max airDate of the next watchable season has passed
        $available = [];
        foreach ($seriesMinSeasons as $seriesTitle => $minSeason) {
            $key = $seriesTitle . '::' . $minSeason;
            if (
                isset($seasonMaxAirDates[$key])
                && $seasonMaxAirDates[$key] !== null
                && $seasonMaxAirDates[$key] <= $now
            ) {
                $available[] = $seriesTitle;
            }
        }

        return $available;
    }
}

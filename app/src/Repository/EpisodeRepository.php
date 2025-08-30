<?php

declare(strict_types=1);

namespace App\Repository;

use App\Document\Episode as EpisodeDocument;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class EpisodeRepository extends DocumentRepository
{
   public function getLatestUnwatchedFromSeries(string $series): ?EpisodeDocument
    {
        $builder = $this->createQueryBuilder()
            ->field('seriesTitle')->equals($series)
            ->field('watched')->equals(false)
            ->sort('season', 'ASC')
            ->sort('episode', 'ASC')
            ->limit(1);

        return $builder->getQuery()->execute()->toArray()[0] ?? null;
    }

    public function getFirstEpisodeForSeries(string $seriesTitle): ?EpisodeDocument
    {
        $builder = $this->createQueryBuilder()
            ->field('seriesTitle')->equals($seriesTitle)
            ->sort('season', 'ASC')
            ->sort('episode', 'ASC')
            ->limit(1);

        return $builder->getQuery()->execute()->toArray()[0] ?? null;
    }

    public function deleteEpisodesWithTvdbSeriesId(string $tvdbSeriesId): void
    {
        $builder = $this->createQueryBuilder()
            ->remove()
            ->field('tvdbSeriesId')->equals($tvdbSeriesId);

        $builder->getQuery()->execute();
    }
}

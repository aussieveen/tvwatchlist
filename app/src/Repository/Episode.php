<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Episode as EpisodeDocument;

class Episode
{
    public function __construct(
        private DocumentManager $documentManager,
        private RecentlyWatched $recentlyWatched
    )
    {
    }

    public function getLatestFromShow(string $show): array
    {
        $builder = $this->documentManager->createQueryBuilder(EpisodeDocument::class)
            ->field('showTitle')->equals($show)
            ->field('watched')->equals(false)
            ->sort('season', 'ASC')
            ->sort('episode', 'ASC')
            ->limit(1);

        return $builder->getQuery()->execute()->toArray();
    }

    public function getLatestFromUniverse(string $universe): array
    {
        $builder = $this->documentManager->createAggregationBuilder(EpisodeDocument::class);
        $builder->match()->field('watched')->equals(false)
            ->match()->field('universe')->equals($universe)
            ->sort('airDate', 'ASC')
            ->limit(1);
        return $builder->getAggregation()->getIterator()->toArray()[0];
    }

    public function getLatestNotRecentlyWatched(): array
    {
        $builder = $this->documentManager->createAggregationBuilder(EpisodeDocument::class);
        $builder->match()->field('watched')->equals(false)
            ->match()->field('showTitle')->notIn($this->recentlyWatched->getShowTitles())
            ->match()->field('universe')->equals('')
            ->group()->field('id')->expression('$showTitle');

        return $builder->getAggregation()->getIterator()->toArray();
    }

}

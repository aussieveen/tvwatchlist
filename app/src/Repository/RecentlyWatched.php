<?php

declare(strict_types=1);

namespace App\Repository;

use App\Document\History;
use Doctrine\ODM\MongoDB\DocumentManager;

class RecentlyWatched
{
    public function __construct(private readonly DocumentManager $documentManager)
    {
    }

    public function getShowTitles(): array
    {
        $builder = $this->documentManager->createAggregationBuilder(History::class);
        $builder->sort('id', 'DESC')->limit(5);
        $results = $builder->getAggregation()->getIterator()->toArray();
        $watched = [];
        foreach ($results as $result) {
            $watched[] = $result['showTitle'];
        }
        return $watched;
    }
}

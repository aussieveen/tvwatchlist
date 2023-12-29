<?php

declare(strict_types=1);

namespace App\Repository;

use App\Document\Episode;
use Doctrine\ODM\MongoDB\DocumentManager;

class TvUniverses
{
    public function __construct(private readonly DocumentManager $documentManager)
    {
    }

    public function getTvUniversesById(): array
    {
        $builder = $this->documentManager->createAggregationBuilder(Episode::class);
        $builder->match()->field('watched')->equals(false)
            ->match()->field('universe')->notEqual('')
            ->group()->field('id')->expression('$universe');
        $universeList = [];
        foreach ($builder->getAggregation()->getIterator()->toArray() as $key => $universe) {
            $universeList[] = $universe['_id'];
        }
        return $universeList;
    }
}

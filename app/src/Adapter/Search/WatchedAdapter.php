<?php

namespace App\Adapter\Search;

use App\Entity\Search\ApiQuery;
use Elastica\Query\BoolQuery;
use Elastica\Query\Term;

class WatchedAdapter
{
    public function adapt(ApiQuery $apiQuery, BoolQuery $boolQuery): void
    {
        $watchedFilter = new Term();
        $watchedFilter->setTerm('watched', $apiQuery->watched->watched);

        $boolQuery->addMust($watchedFilter);
    }
}

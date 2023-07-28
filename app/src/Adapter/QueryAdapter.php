<?php

namespace App\Adapter;

use App\Adapter\Search\WatchedAdapter;
use App\Entity\Search\ApiQuery;
use Elastica\Aggregation\Terms;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\FunctionScore;
use Elastica\QueryBuilder\DSL\Aggregation;

class QueryAdapter
{
    public function __construct(
        private readonly WatchedAdapter $watchedAdapter
    )
    {
    }

    public function adapt(ApiQuery $apiQuery): Query
    {
        [$boolQuery, $functionScoreQuery, $query] = $this->getQueryObjects();
        $this->watchedAdapter->adapt($apiQuery, $boolQuery);
        return $this->combineQueryObjects($boolQuery, $functionScoreQuery, $query);
    }

    private function getQueryObjects(): array
    {
        return [new BoolQuery(), new FunctionScore(), new Query()];
    }

    private function combineQueryObjects(BoolQuery $boolQuery, FunctionScore $functionScoreQuery, Query $query): Query
    {
        $functionScoreQuery->setQuery($boolQuery);
        $query->setQuery($functionScoreQuery);

        return $query;
    }
}

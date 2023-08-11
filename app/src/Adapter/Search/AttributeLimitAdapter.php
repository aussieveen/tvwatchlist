<?php

namespace App\Adapter\Search;

use App\Entity\Search\ApiQuery;
use Elastica\Aggregation\Terms as TermsAggregation;
use Elastica\Aggregation\TopHits;
use Elastica\Query;

class AttributeLimitAdapter
{
    public function adapt(ApiQuery $apiQuery, Query $query): void
    {
        $termAggregation = new TermsAggregation('showTitles');
        $termAggregation->setField($apiQuery->attributeLimit->attribute . '.keyword');
        $termAggregation->setSize($apiQuery->attributeLimit->limit);

        $topHits = new TopHits('top');

        # Set Hierarchy
        $termAggregation->addAggregation($topHits);

        $topHits->setParams([
            'size' => 1,
            '_source' => [
                'includes' => [
                    $apiQuery->attributeLimit->attribute
                ],
            ],
        ]);

        $query->addAggregation($termAggregation);
    }
}

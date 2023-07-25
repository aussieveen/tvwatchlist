<?php

namespace App\Entity\Search;

use App\Entity\Search\Query\Watched;

readonly class ApiQuery
{
    public function __construct(
        Watched $watched
    )
    {
    }
}
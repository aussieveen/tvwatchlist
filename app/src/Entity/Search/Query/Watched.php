<?php

namespace App\Entity\Search\Query;

readonly class Watched
{
    public function __construct(public bool $watched)
    {
    }
}
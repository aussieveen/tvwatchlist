<?php

namespace App\Entity\Tvdb\Data\Ingest;

readonly class Criteria
{
    public function __construct(
        public int $seriesId,
        public int $season,
        public int $episode,
        public string $platform
    )
    {
    }
}
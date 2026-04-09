<?php

declare(strict_types=1);

namespace App\Entity\Tvdb;

readonly class Episode
{
    public function __construct(
        public string $tvdbId,
        public string $title,
        public string $overview,
        public ?string $aired,
        public int $seasonNumber,
        public int $number
    ) {
    }
}

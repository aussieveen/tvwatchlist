<?php

declare(strict_types=1);

namespace App\Entity\Tvdb\Api;

use JsonSerializable;

readonly class Show implements JsonSerializable
{
    public function __construct(
        public string $id,
        public string $title,
        public string $overview,
        public string $poster,
        public ?int $year
    ) {
    }

    public function jsonSerialize(): array
    {
        return (array) $this;
    }
}

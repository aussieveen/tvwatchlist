<?php

declare(strict_types=1);

namespace App\Entity\Tvdb\Api\ApiQuery;

class ShowTitle
{
    public function __construct(public readonly string $title)
    {
    }
}

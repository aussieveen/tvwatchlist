<?php

declare(strict_types=1);

namespace App\Entity\Tvdb\Api;

use App\Entity\Tvdb\Api\ApiQuery\ShowTitle;

readonly class ApiQuery
{
    public function __construct(
        public ShowTitle $showTitle
    ) {
    }
}

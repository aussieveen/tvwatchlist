<?php

declare(strict_types=1);

namespace App\Document;

use App\Traits\HasValues;

enum EpisodePlatforms: string
{
    use HasValues;

    case PLEX = 'Plex';
    case NETFLIX = 'Netflix';
    case DISNEY_PLUS = 'Disney Plus';
    case AMAZON_PRIME = 'Amazon Prime';
}

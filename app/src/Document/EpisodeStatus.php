<?php

declare(strict_types=1);

namespace App\Document;

use App\Traits\HasValues;

enum EpisodeStatus: string
{
    use HasValues;
    public final const STATUS_AIRING = 1;
    public final const STATUS_FINISHED = 2;
    public final const STATUS_UPCOMING = 3;

    case AIRING = 'airing';
    case FINISHED = 'finished';
    case UPCOMING = 'upcoming';

    public static function fromTvdbStatus(int $tvdbStatus): self
    {
        return match ($tvdbStatus) {
            1 => self::AIRING,
            2 => self::FINISHED,
            3 => self::UPCOMING,
        };
    }
}

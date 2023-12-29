<?php

declare(strict_types=1);

namespace App\Factory;

use App\Document\Episode;
use DateTimeInterface;

class EpisodeDocumentFactory
{
    public function build(
        string $title,
        string $tvdbEpisodeId,
        string $description,
        int $season,
        int $episode,
        string $showTitle,
        string $tvdbSeriesId,
        string $poster,
        string $platform,
        string $status,
        DateTimeInterface $airDate,
        string $universe,
        Episode $episodeDocument = null,
    ): Episode {
        if (!$episodeDocument) {
            $episodeDocument = new Episode();
        }
        $episodeDocument->title = $title;
        $episodeDocument->tvdbEpisodeId = $tvdbEpisodeId;
        $episodeDocument->description = $description;
        $episodeDocument->season = $season;
        $episodeDocument->episode = $episode;
        $episodeDocument->showTitle = $showTitle;
        $episodeDocument->tvdbSeriesId = $tvdbSeriesId;
        $episodeDocument->poster = $poster;
        $episodeDocument->platform = $platform;
        $episodeDocument->status = $status;
        $episodeDocument->airDate = $airDate;
        $episodeDocument->universe = $universe;

        return $episodeDocument;
    }
}

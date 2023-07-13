<?php

namespace App\Document;

use DateTimeInterface;

class EpisodeFactory
{
    public function build(
        string $title,
        string $description,
        int $season,
        int $episode,
        string $showTitle,
        string $poster,
        string $platform,
        string $status,
        DateTimeInterface $airDate,
    ): Episode
    {
        $episodeDocument = new Episode();
        $episodeDocument->title = $title;
        $episodeDocument->description = $description;
        $episodeDocument->season = $season;
        $episodeDocument->episode = $episode;
        $episodeDocument->showTitle = $showTitle;
        $episodeDocument->poster = $poster;
        $episodeDocument->platform = $platform;
        $episodeDocument->status = $status;
        $episodeDocument->airDate = $airDate;

        return $episodeDocument;
    }
}
<?php

declare(strict_types=1);

namespace App\Helper;

use App\Repository\Episode;
use App\Repository\RecentlyWatched;
use App\Repository\TvUniverses;

class EpisodeSelector
{
    public function __construct(
        private readonly RecentlyWatched $recentlyWatched,
        private readonly TvUniverses $tvUniverses,
        private readonly Episode $episode
    ) {
    }

    public function getShowNotOnRecentlyWatchedList(): string
    {
        $showList = [];
        $recentlyWatched = $this->recentlyWatched->getShowTitles();

        // Get the next unwatched show, not recently watched from each universe and add to the list.
        foreach ($this->tvUniverses->getTvUniversesById() as $universe) {
            $show = $this->episode->getLatestFromUniverse($universe);
            if (!in_array($show['showTitle'], $recentlyWatched)) {
                $showList[] = $show['showTitle'];
            }
        }

        // Get all shows that are not recently watched and not in a universe.
        foreach ($this->episode->getLatestNotRecentlyWatched() as $show) {
            $showList[] = $show['_id'];
        }

        return empty($showList) ? '' : $showList[array_rand($showList)];
    }

    public function getShowFromRecentlyWatchedList(): string
    {
        $seriesWithWatchableEpisodes = $this->episode->getSeriesWithWatchableEpisodes();
        $series = [];
        foreach ($seriesWithWatchableEpisodes as $s) {
            $series[] = $s['_id'];
        }
        $recentlyWatched = $this->recentlyWatched->getShowTitles();

        $recentlyWatched = array_intersect($recentlyWatched, $series);
        $recentlyWatchedCount = count($recentlyWatched);

        if ($recentlyWatchedCount === 0) {
            return '';
        }

        $showCounts = array_count_values($recentlyWatched);

        // If there is only one show in the list, return it.
        // If there is more than one show in the list and they are all the same, return it.
        if ($recentlyWatchedCount === 1 || count($showCounts) === 1) {
            return $recentlyWatched[0];
        }

        // If there are only two shows in the list, return the one that is not the most recent.
        if (count($showCounts) === 2) {
            unset($showCounts[$recentlyWatched[0]]);
            return array_keys($showCounts)[0];
        }

        // Remove all instances of the first show, if the count is now 1, return the show.
        $filteredShows = array_values(array_diff($recentlyWatched, [$recentlyWatched[0]]));
        if (count($filteredShows) === 1) {
            return $filteredShows[0];
        }

        $upNext = '';
        $seenBefore = [];
        foreach ($filteredShows as $key => $show) {
            if ($key === 0) {
                $upNext = $show;
                $seenBefore[] = $show;
                continue;
            }
            if (!in_array($show, $seenBefore)) {
                $upNext = $show;
                $seenBefore[] = $show;
            }
        }

        return $upNext;
    }
}

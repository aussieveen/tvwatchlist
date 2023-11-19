<?php

namespace App\Tests\Helper;

use App\Helper\EpisodeSelector;
use App\Repository\Episode;
use App\Repository\RecentlyWatched;
use App\Repository\TvUniverses;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class EpisodeSelectorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private EpisodeSelector $unit;
    private RecentlyWatched $recentlyWatched;
    public function setUp(): void
    {
        $this->recentlyWatched = Mockery::mock(RecentlyWatched::class);
        $tvUniverses = Mockery::mock(TvUniverses::class);
        $episode = Mockery::mock(Episode::class);

        $this->unit = new EpisodeSelector(
            $this->recentlyWatched,
            $tvUniverses,
            $episode
        );
    }

    /**
     * @dataProvider getRecentlyWatchedDataProvider
     */
    public function testGetShowFromRecentlyWatchedList($watched, $expected): void
    {
        $this->recentlyWatched->expects('getShowTitles')
            ->andReturns($watched);

        $this->assertSame($expected, $this->unit->getShowFromRecentlyWatchedList());
    }

    public static function getRecentlyWatchedDataProvider(): array
    {
        return [
            'empty' => [
                'recentlyWatched' => [],
                'expected' => '',
            ],
            'one item in list' => [
                'recentlyWatched' => ['show1'],
                'expected' => 'show1',
            ],
            'only two items in list' => [
                'recentlyWatched' => ['show1', 'show2'],
                'expected' => 'show2',
            ],
            'two same items in list' => [
                'recentlyWatched' => ['show1', 'show1'],
                'expected' => 'show1',
            ],
            'three unique items' => [
                'recentlyWatched' => ['show1', 'show2', 'show3'],
                'expected' => 'show3',
            ],
            'four items, two shows, equal spread' => [
                'recentlyWatched' => ['show4', 'show2', 'show2', 'show4'],
                'expected' => 'show2',
            ],
            'four items, two shows, three of one, one of one' => [
                'recentlyWatched' => ['show4', 'show2', 'show4', 'show4'],
                'expected' => 'show2',
            ],
            'four items, three shows' => [
                'recentlyWatched' => ['show4', 'show2', 'show3', 'show4'],
                'expected' => 'show3',
            ],
            'five items, four shows' => [
                'recentlyWatched' => ['show5', 'show2', 'show3', 'show4', 'show5'],
                'expected' => 'show4',
            ],
            'five items, two shows' => [
                'recentlyWatched' => ['show1', 'show2', 'show2', 'show2', 'show1'],
                'expected' => 'show2',
            ],
            'five items, three shows, evenly spread' => [
                'recentlyWatched' => ['show1', 'show2', 'show3', 'show1', 'show2'],
                'expected' => 'show3',
            ],
            'five items, three shows, awkwardly spread' => [
                'recentlyWatched' => ['show1', 'show3', 'show2', 'show2', 'show2'],
                'expected' => 'show2',
            ],
            'five items, three shows, 1-3-2-3-2' => [
                'recentlyWatched' => ['show1', 'show3', 'show2', 'show3', 'show2'],
                'expected' => 'show2',
            ]
        ];
    }
}

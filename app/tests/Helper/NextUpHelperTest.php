<?php

namespace App\Tests\Helper;

use App\Helper\NextUpHelper;
use App\Repository\Series;
use DateTimeInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NextUpHelperTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private NextUpHelper $unit;
    private Series $series;
    public function setUp(): void
    {
        $this->series = Mockery::mock(Series::class);

        $this->unit = new NextUpHelper(
            $this->series
        );
    }

    public function testGetSeriesNotOnRecentlyWatchedList(): void
    {
        $this->series->shouldReceive('getSeriesTitlesWithAvailableCurrentSeason')
            ->with(Mockery::type(DateTimeInterface::class))
            ->andReturn(['legends of tomorrow', 'daredevil', 'succession']);

        $this->series->shouldReceive('getLatestTitleFromUniverse')
            ->with('dc')
            ->andReturn('legends of tomorrow');
        $this->series->shouldReceive('getLatestTitleFromUniverse')
            ->with('marvel')
            ->andReturn('daredevil');

        $this->series->shouldReceive('getTitlesRecentlyWatched')
            ->andReturn(['the flash','arrow','jessica jones','schitts creek']);
        $this->series->shouldReceive('getUniverses')
            ->andReturn(['dc','marvel']);
        $this->series->shouldReceive('getTitlesNotRecentlyWatchedAndNotInAnUniverse')
            ->andReturn(['succession']);

        for ($i = 0; $i < 100; $i++) {
            $actual = $this->unit->getSeriesNotOnRecentlyWatchedList();

            $this->assertNotSame('the flash', $actual);
            $this->assertNotSame('arrow', $actual);
            $this->assertNotSame('schitts creek', $actual);
        }
    }

    public function testGetSeriesNotOnRecentlyWatchedListExcludesUnairedSeason(): void
    {
        $this->series->shouldReceive('getSeriesTitlesWithAvailableCurrentSeason')
            ->with(Mockery::type(DateTimeInterface::class))
            ->andReturn(['legends of tomorrow']);

        $this->series->shouldReceive('getLatestTitleFromUniverse')
            ->with('dc')
            ->andReturn('legends of tomorrow');
        $this->series->shouldReceive('getLatestTitleFromUniverse')
            ->with('marvel')
            ->andReturn('daredevil');

        $this->series->shouldReceive('getTitlesRecentlyWatched')
            ->andReturn([]);
        $this->series->shouldReceive('getUniverses')
            ->andReturn(['dc', 'marvel']);
        $this->series->shouldReceive('getTitlesNotRecentlyWatchedAndNotInAnUniverse')
            ->andReturn(['succession']);

        for ($i = 0; $i < 20; $i++) {
            $actual = $this->unit->getSeriesNotOnRecentlyWatchedList();
            $this->assertNotSame('daredevil', $actual, 'daredevil should be excluded as current season has not fully aired');
            $this->assertNotSame('succession', $actual, 'succession should be excluded as current season has not fully aired');
            $this->assertSame('legends of tomorrow', $actual);
        }
    }

    public function testGetSeriesNotOnRecentlyWatchedListWithNoShows(): void
    {
        $this->series->expects('getSeriesTitlesWithAvailableCurrentSeason')
            ->with(Mockery::type(DateTimeInterface::class))
            ->andReturn([]);
        $this->series->expects('getUniverses')
            ->andReturns([]);
        $this->series->expects('getTitlesRecentlyWatched')
            ->andReturn([]);
        $this->series->expects('getTitlesNotRecentlyWatchedAndNotInAnUniverse')
            ->andReturn([]);

        $this->assertSame('', $this->unit->getSeriesNotOnRecentlyWatchedList());
    }

    #[DataProvider('getRecentlyWatchedDataProvider')]
    public function testGetShowFromRecentlyWatchedList($watched, $expected): void
    {
        $this->series->expects('getTitlesWithWatchableEpisodes')
            ->andReturns(['show1', 'show2', 'show3', 'show4', 'show5']);
        $this->series->expects('getSeriesTitlesWithAvailableCurrentSeason')
            ->with(Mockery::type(DateTimeInterface::class))
            ->andReturns(['show1', 'show2', 'show3', 'show4', 'show5']);
        $this->series->expects('getTitlesRecentlyWatched')
            ->andReturns($watched);

        $this->assertSame($expected, $this->unit->getSeriesFromRecentlyWatchedList());
    }

    public function testGetShowFromRecentlyWatchedListExcludesUnairedSeason(): void
    {
        $this->series->expects('getTitlesWithWatchableEpisodes')
            ->andReturns(['show1', 'show2', 'show3']);
        $this->series->expects('getSeriesTitlesWithAvailableCurrentSeason')
            ->with(Mockery::type(DateTimeInterface::class))
            ->andReturns(['show1', 'show3']);
        $this->series->expects('getTitlesRecentlyWatched')
            ->andReturns(['show1', 'show2']);

        $this->assertSame('show1', $this->unit->getSeriesFromRecentlyWatchedList());
    }

    public static function getRecentlyWatchedDataProvider(): array
    {
        return [
            'empty' => [[], ''],
            'one item in list' => [['show1'], 'show1'],
            'only two items in list' => [['show1', 'show2'], 'show2'],
            'two same items in list' => [['show1', 'show1'], 'show1'],
            'two items but one is not in watchable list' => [['show1', 'show6'], 'show1'],
            'three unique items' => [['show1', 'show2', 'show3'], 'show3'],
            'four items, two shows, equal spread' => [['show4', 'show2', 'show2', 'show4'], 'show2'],
            'four items, two shows, three of one, one of one' => [['show4', 'show2', 'show4', 'show4'], 'show2'],
            'four items, three shows' => [['show4', 'show2', 'show3', 'show4'], 'show3'],
            'five items, four shows' => [['show5', 'show2', 'show3', 'show4', 'show5'], 'show4'],
            'five items, two shows' => [['show1', 'show2', 'show2', 'show2', 'show1'], 'show2'],
            'five items, three shows, evenly spread' => [['show1', 'show2', 'show3', 'show1', 'show2'], 'show3'],
            'five items, three shows, awkwardly spread' => [['show1', 'show3', 'show2', 'show2', 'show2'], 'show2'],
            'five items, three shows, 1-3-2-3-2' => [['show1', 'show3', 'show2', 'show3', 'show2'], 'show2'],
        ];
    }
}

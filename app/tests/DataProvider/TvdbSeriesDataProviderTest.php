<?php

namespace App\Tests\DataProvider;

use App\Api\TvdbQueryClient;
use App\DataProvider\TvdbSeriesDataProvider;
use App\Entity\Tvdb\Episode;
use App\Processor\TvdbEpisodeData;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\ResponseInterface;

class TvdbSeriesDataProviderTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private TvdbSeriesDataProvider $unit;
    private TvdbQueryClient $client;
    private TvdbEpisodeData $episodeDataProcessor;
    private ResponseInterface $seriesResponseInterface;
    private ResponseInterface $seasonResponseInterface;

    public function setUp(): void
    {
        $this->seriesResponseInterface = Mockery::mock(ResponseInterface::class);
        $this->seasonResponseInterface = Mockery::mock(ResponseInterface::class);
        $this->client = Mockery::mock(TvdbQueryClient::class);
        $this->client->allows('seriesExtended')
            ->with(123)
            ->andReturn($this->seriesResponseInterface)
            ->byDefault();
        $this->client->allows('seasonExtended')
            ->with(456)
            ->andReturn($this->seasonResponseInterface)
            ->byDefault();

        $this->episodeDataProcessor = new TvdbEpisodeData();


        $this->unit = new TvdbSeriesDataProvider($this->client, $this->episodeDataProcessor);
    }

    public function testGetSeriesReturnsNullWhenStatusNotSuccess(): void
    {
        $this->seriesResponseInterface->expects('getContent')->andReturn(json_encode([
            'status' => 'failure',
        ]));

        $this->assertNull($this->unit->getSeries('123'));
    }

    /**
     * @dataProvider invalidSeriesDataProvider
     */
    public function testGetSeriesReturnBaseInfo(array $invalidData, int $fromSeason = 1): void
    {
        $this->seriesResponseInterface->expects('getContent')->andReturn(json_encode([
            'status' => 'success',
            'data' => array_merge([
                'id' => '123',
                'name' => 'Test Series',
                'image' => 'https://www.example.com/image.jpg',
                'status' => [
                    'id' => 1,
                ],
            ], $invalidData),
        ]));

        $actual = $this->unit->getSeries('123', $fromSeason);

        $this->assertSame('Test Series', $actual->title);
        $this->assertSame('https://www.example.com/image.jpg', $actual->poster);
        $this->assertSame(1, $actual->status);
    }

    public static function invalidSeriesDataProvider(): array
    {
        return [
            'no seasons' => [
                [
                    'seasons' => [],
                ],
            ],
            'no regular seasons' => [
                [
                    'seasons' => [
                        [
                            'type' => [
                                'id' => 2,
                            ],
                        ],
                    ],
                ],
            ],
            'season before fromSeason' => [
                [
                    'seasons' => [
                        [
                            'type' => [
                                'id' => 1,
                            ],
                            'number' => 1,
                        ],
                    ],
                ],
                2,
            ],
        ];
    }

    public function testGetSeriesReturnsNullWhenSeasonsResponseNotSuccess(): void
    {
        $this->seriesResponseInterface->expects('getContent')->andReturn(json_encode([
            'status' => 'success',
            'data' => [
                'id' => '123',
                'name' => 'Test Series',
                'image' => 'https://www.example.com/image.jpg',
                'status' => [
                    'id' => 1,
                ],
                'seasons' => [
                    [
                        'id' => '456',
                        'type' => [
                            'id' => 1,
                        ],
                        'number' => 1,
                    ],
                ],
            ],
        ]));

        $this->seasonResponseInterface->expects('getContent')->andReturn(json_encode([
            'status' => 'failure',
        ]));

        $this->assertNull($this->unit->getSeries('123'));
    }

    public function testGetSeriesReturnsExpectedSeries(): void
    {
        $this->seriesResponseInterface->expects('getContent')->andReturn(json_encode([
            'status' => 'success',
            'data' => [
                'id' => '123',
                'name' => 'Test Series',
                'image' => 'https://www.example.com/image.jpg',
                'status' => [
                    'id' => 1,
                ],
                'seasons' => [
                    [
                        'id' => '456',
                        'type' => [
                            'id' => 1,
                        ],
                        'number' => 1,
                    ],
                    [
                        'id' => '789',
                        'type' => [
                            'id' => 1,
                        ],
                        'number' => 2,
                    ],
                ],
            ],
        ]));

        $this->seasonResponseInterface->expects('getContent')
            ->andReturn(json_encode([
            'status' => 'success',
            'data' => [
                'episodes' => [
                    [
                        'id' => '101112',
                        'name' => 'Test Episode',
                        'overview' => 'Test Overview',
                        'seasonNumber' => 1,
                        'number' => 1,
                        'aired' => '2019-01-01',
                    ],
                ],
            ],
        ]));

        $this->client->expects('seasonExtended')
            ->with('456')
            ->andReturn($this->seasonResponseInterface);

        $secondSeasonResponseInterface = Mockery::mock(ResponseInterface::class);
        $secondSeasonResponseInterface->expects('getContent')
            ->andReturn(json_encode([
                'status' => 'success',
                'data' => [
                    'episodes' => [
                        [
                            'id' => '131415',
                            'name' => 'Test Episode 2',
                            'overview' => 'Test Overview 2',
                            'seasonNumber' => 2,
                            'number' => 1,
                            'aired' => '2020-01-01',
                        ],
                    ],
                ],
            ]));

        $this->client->expects('seasonExtended')
            ->with('789')
            ->andReturn($secondSeasonResponseInterface);

        $actual = $this->unit->getSeries('123');

        $this->assertEquals([
            101 => new Episode(
                '101112',
                'Test Episode',
                'Test Overview',
                '2019-01-01',
                1,
                1
            ),
            201 => new Episode(
                '131415',
                'Test Episode 2',
                'Test Overview 2',
                '2020-01-01',
                2,
                1
            ),
        ], $actual->getEpisodes());
    }
}

<?php

namespace App\Api;

use App\Entity\Tvdb\Api\ApiQuery\ShowTitle;
use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class TvdbClient
{
    private const BASE_URL = 'https://api4.thetvdb.com/v4/';
    private const TOKEN = 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJhZ2UiOiIiLCJhcGlrZXkiOiI1ODBjOTIzOS1kMmY4LTQ0NjAtYTIyZS02ODMxOTAwYTk3YTMiLCJjb21tdW5pdHlfc3VwcG9ydGVkIjp0cnVlLCJleHAiOjE2OTc5NTE4MTQsImdlbmRlciI6IiIsImhpdHNfcGVyX2RheSI6MTAwMDAwMDAwLCJoaXRzX3Blcl9tb250aCI6MTAwMDAwMDAwLCJpZCI6IjEiLCJpc19tb2QiOnRydWUsImlzX3N5c3RlbV9rZXkiOmZhbHNlLCJpc190cnVzdGVkIjpmYWxzZSwicGluIjoiSVBRWk9DTU4iLCJyb2xlcyI6WyJNb2QiXSwidGVuYW50IjoidHZkYiIsInV1aWQiOiIifQ.BSFvnwIuFdeNF3mLp1jC4v2bTBl7DTwy4iUXd1umoDkbgr67H2QBKLL_nE8oFaeB55_Fa6pvTMBLq6_RGKiYWjGHy09i2Pqo_M02yGV084tS4SJOuWjaHGDDeBJf5ytyVVrzXx2Ze59cUR9yCa7NGGtiH-kaphSXN8ac5q8if1pyA7Y-or4gn4k3vuCtOan4xtARs2rvoubJd3fm7Ts1M7PMyUvr6LEneGbgCG6O4W_-h5igim-AvId0XEyoSAbcB9R-Ct16mgTfu9XkkGbznMcpyZxZ0-a8UvCAelAzJz2hjrVg_FNlpvy9dY8vs_TVf8939M96vZKYjHDkuV86-tc48IEjOw8lkb9ELW2YhhtXVqWaeIIdX9q4hmo5nq3CWdLyIyRrPiZfZKCSGDzDoF5XraOvP9kM0Uj7VLRyG4CLZC28PS-NlLxKXVVe13dvFKolQCUWGqDzdFnf-dyEIwGC9o_IDI49duRkPx-MRl1GRXzWCRp3ZY8n3ZTLSCFz2zr3_uOeX8EYLEehx8QTpM0HDGXnro5kQGdHV0fhgor7KPyTmt3hFpu6tvRcw2ZhsCBAEW9DGh4Mdh7vgyh0Txyhr352rfJI5_RP0dqRZdI3mB43bWg1DpovGAkm2mdBXHUkOhzKr9oTVh3i2pInq2E1JUwFvCBQm__LMKrZ9BI';
    public function __construct(
        private HttpClientInterface $httpClient
    ) {
    }

    public function search(ShowTitle $showTitle): ResponseInterface
    {
        try {
            return $this->httpClient->request(
                'GET',
                self::BASE_URL . 'search',
                [
                    'query' => [
                        'query' => $showTitle->title,
                        'type' => 'series'
                    ],
                    'headers' => [
                        'Authorization' => 'Bearer ' . self::TOKEN
                    ]
                ]
            );
        } catch (TransportExceptionInterface $e) {
            throw new RuntimeException('Error while searching for show', 0, $e);
        }
    }

    public function seriesExtended(int $seriesId): ResponseInterface
    {
        try {
            return $this->httpClient->request(
                'GET',
                self::BASE_URL . 'series/' . $seriesId . '/extended',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . self::TOKEN
                    ]
                ]
            );
        } catch (TransportExceptionInterface $e) {
            throw new RuntimeException('Error while getting series', 0, $e);
        }
    }

    public function seasonExtended(int $seasonId): ResponseInterface
    {
        try {
            return $this->httpClient->request(
                'GET',
                self::BASE_URL . 'seasons/' . $seasonId . '/extended',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . self::TOKEN
                    ]
                ]
            );
        } catch (TransportExceptionInterface $e) {
            throw new RuntimeException('Error while getting season data', 0, $e);
        }
    }
}

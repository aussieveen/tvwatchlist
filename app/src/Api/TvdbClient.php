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
    private const TOKEN = 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJhZ2UiOiIiLCJhcGlrZXkiOiI1ODBjOTIzOS1kMmY4LTQ0NjAtYTIyZS02ODMxOTAwYTk3YTMiLCJjb21tdW5pdHlfc3VwcG9ydGVkIjp0cnVlLCJleHAiOjE3MDQ2NTYzODMsImdlbmRlciI6IiIsImhpdHNfcGVyX2RheSI6MTAwMDAwMDAwLCJoaXRzX3Blcl9tb250aCI6MTAwMDAwMDAwLCJpZCI6IjEiLCJpc19tb2QiOnRydWUsImlzX3N5c3RlbV9rZXkiOmZhbHNlLCJpc190cnVzdGVkIjpmYWxzZSwicGluIjoiSVBRWk9DTU4iLCJyb2xlcyI6WyJNb2QiXSwidGVuYW50IjoidHZkYiIsInV1aWQiOiIifQ.sU_yaXmWL7FJlJC9-fSfHG_9rEj9-vixrZ2CfVxPe0A7TbbGxuhJJefgh2jAjoHpNk27mfgmSBDkwgAmtxpfkx9-wOAgLwAa1Ru-5l0m9Jji3QRjBi4SkboT4LV7jbJXek_sJi2ep2nztrQHZeAyx6k-xnnosXqGZKiHlHA0s5FrsMUc4dgv2t8i3AwDql6epvJdKYOLJ7XxXbVYAOKzcCLet-v73XlXO-OMqwXURZLfWpfHZWZ-8zbXw4dynWJrV_qxqTWBWd1xU57jowtcgcn89BhiffLeZ1L_-VvPLcHZ_pmPFHW7uQXLpy_CkJpcCxN6GWtqWqavy0AQRHEvdRFwpTmBZKhsXCybF_KDd87Mgxh6Cy4nSVUAwLz4NJFVeLKbDiLMakhGtHqA5vXbOLA4AiI59-cCtVCBvRNs8jO_dgXMqKiunJVXd-Vv9EYTWvSXplDJFVpb5EjiF0zH03NKCuGC4KVxLFuTQlc0VXf6CzhAnCNQSGDWZSFHZzO7QuccVAh9tgnLFywcKUQNqoXIbjOZKC-xfWRlcxl4kX8ih0JEtcHxntWNmOEqrmUzpL6tJ-BfkoBHLDlN18iw4lQw98uZcXaks8t4YfitjHRjJpvgndvXXVbGCuCe0ddZrb6qLkfFXuN-CnojBy6-uqEEraSHk4rwBCRhty8p2mE';
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

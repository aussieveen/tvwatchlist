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
    private const TOKEN = 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJhZ2UiOiIiLCJhcGlrZXkiOiI1ODBjOTIzOS1kMmY4LTQ0NjAtYTIyZS02ODMxOTAwYTk3YTMiLCJjb21tdW5pdHlfc3VwcG9ydGVkIjp0cnVlLCJleHAiOjE3MDE2Njg2MTMsImdlbmRlciI6IiIsImhpdHNfcGVyX2RheSI6MTAwMDAwMDAwLCJoaXRzX3Blcl9tb250aCI6MTAwMDAwMDAwLCJpZCI6IjEiLCJpc19tb2QiOnRydWUsImlzX3N5c3RlbV9rZXkiOmZhbHNlLCJpc190cnVzdGVkIjpmYWxzZSwicGluIjoiSVBRWk9DTU4iLCJyb2xlcyI6WyJNb2QiXSwidGVuYW50IjoidHZkYiIsInV1aWQiOiIifQ.IFEMZFTOnHsXdI6lUrRTX2vy8NlYzitMxHEdgTLAmeGQmqvBm6-SkzHoGaK3CEkDHRTJjgZFzoB4UZaahyTk8TKJsWtWQrNH4xK8Re1_FhQMgZ6OedvQbkjPtH5JjAfg795Ify7xrvuLlwgPqCzGlwtXL6VlX0yfjKJ8uczg1se61wcuhwGO-iQLPiKmuTah7odrc83TFm0M4MQnUpUi08jjp7oVcB-YU-7Jwxlxdnu8Vix86c311jb8YL4XsMUOJFeEqYDrmXo871JXfmArWORSxGd_-OV-p4un6mLXB8U7PsUAbmEVthkmVGRdf8kZgfH4C_e135MnOBcKPfTT2zSpFOGdlO1DnnTiycjSD4oqesgw2ulX__knV3MgSpesplUHc6EtQMqRzsBcYhAjXzAL1m4mFGVIS3dWt-kKANDX-3Q9KKayiGl6fZZ681qrKbgdBxqO-Lslt173e9d9TSzVb1Syj8O9OIyf1NmlNwHCCNMHI6nMxKdTkSR0ono7fByf6c6bo7cb7I0IXz2RKP9LTUN1VfI2IxiaQ1E66kZ0VW_o9XN2nPmH93k4GxSTUb0AMXsjVOvI7RgjwHO-wuXgmM1jX7JDH_6Gf0sWI9yGJeEPuzkn_PIShFiwxSSXaHYK8dEYVm28uhGteoG5sPb2nmfBv9TQHOjwwCxFjeA';
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

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
    private const TOKEN = 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJhZ2UiOiIiLCJhcGlrZXkiOiI1ODBjOTIzOS1kMmY4LTQ0NjAtYTIyZS02ODMxOTAwYTk3YTMiLCJjb21tdW5pdHlfc3VwcG9ydGVkIjp0cnVlLCJleHAiOjE2OTQwNjUxNzMsImdlbmRlciI6IiIsImhpdHNfcGVyX2RheSI6MTAwMDAwMDAwLCJoaXRzX3Blcl9tb250aCI6MTAwMDAwMDAwLCJpZCI6IjEiLCJpc19tb2QiOnRydWUsImlzX3N5c3RlbV9rZXkiOmZhbHNlLCJpc190cnVzdGVkIjpmYWxzZSwicGluIjoiSVBRWk9DTU4iLCJyb2xlcyI6WyJNb2QiXSwidGVuYW50IjoidHZkYiIsInV1aWQiOiIifQ.bvz6hLVV0RG_usdNP5yKBN2a5c-DXJ7hjdKRfoh4mBGGctL_1GgjFFXrLCQDCoLQTr31FOPnXs9Y-k9AYc_QBEdRhgvoeKFGyb4YXZF1X70KIHYaFkwQG5b7UX6uUo-KdCtd5gDBBBBaMSD16-TAGcE_Ibcni8vSkxCmRXPOcNKptSdBhbJ3t26uPoA7MLJdkXFoYdZs9MDyXZg8gdRUGLRoihXQk660Udw0XTCDI7m2XQbZAvsv1eilVf4QZeavCFeZ2btahWJb4-9D_jsVnv_3gi1OLRg-nepgHBCuI5m_egAAPS_OiV2N5_FYliiutPwG8fdSLdJPOhlNATriG7ZjfVKW0-om5MkVLS9j5IybuFbkjjhe3Q1loWtNMSGUoLG5KKC4NrVbhSKG1L7C1B3GD52U4K64aIfdyFblSW-oy8bwDpvBJZrzwnKTzel6Nw7blg7YsAaoGzpAp-OWHVv60U0aHqgodvQyWaD-xjGRhP97rWYyiO_N7ifhMJsWfGGXUEWOuAvWM2Egn7ZkObORB5jFb74xLNyunaa9agEpzeBxJvSyThmHwomfcGNqX5lcNb7rd60twrRpVYiTgpTwyCc-oBWvspyqd8Oy-sQ8o_x0tcS4rYsVWqui2gpC02J5htLo2BGaZrcf-bqz3XlPXGhNytouja_T3-SvaUg';
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

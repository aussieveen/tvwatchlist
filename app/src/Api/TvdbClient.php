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
    private const TOKEN = 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJhZ2UiOiIiLCJhcGlrZXkiOiI4YzM3N2MwZi1jN2E1LTRkYTUtYjM4MC03MjBkZTM1MzFhN2IiLCJjb21tdW5pdHlfc3VwcG9ydGVkIjp0cnVlLCJleHAiOjE2OTE0MzI5ODUsImdlbmRlciI6IiIsImhpdHNfcGVyX2RheSI6MTAwMDAwMDAwLCJoaXRzX3Blcl9tb250aCI6MTAwMDAwMDAwLCJpZCI6IjIxMzYwNCIsImlzX21vZCI6ZmFsc2UsImlzX3N5c3RlbV9rZXkiOmZhbHNlLCJpc190cnVzdGVkIjpmYWxzZSwicGluIjoiQVQ5Ulo3Q1UiLCJyb2xlcyI6W10sInRlbmFudCI6InR2ZGIiLCJ1dWlkIjoiIn0.NzY2QUcwzObn_U7vTHRyQZgWDjQuGboDLBVbNaV4ZwBCM02TlgJOEwKlwQsKzA8LpxNYJuTIzPB4f1XpfGQtr58x6Fjnm9gPouSP5QOzO94ZyYYDIq0MzFHrqLyQ8XGRbYHWM71srYeqPgm9kc6Gt7RclBLdTnuT8lzbYMIqHsex0pcvIeN64Y5Wb9PC-OwLwQuWQxHMZ7m8NgpXFuJlvlgTNex_D2LFKK6sYXpEbeshuWtvvxwcuDftzSKnDFr8bhUvkdHS-YyUozXu5QSr6aSd9IAQvLd3SipX1uVcF60OLdoL3LPgcIruVKBtRjXhnKkX2RWpWdgadDDHNa_LTLeApoyBDPKCCb796_gM04tbLqL71RRYeqYjve8Cv31nQw_JQh3zXAuDSSUrdLdNL7NsKx8Xye8o7V5fiLqH8b_TzB2rc1JzvIIBdC_FF9h2qeRD55crxAJGO4tg-J6K2mxX7iV4PQkmclxcdc0qI6HWdS5wM76bynswZ8NFd1duiMGCLQhI_GiwSQb-xKEdxtxB3FcgpvRFyv-e1F7lqmo0jHSbxhrSambpKe9_QGyE-hPfXc2wvysf7LJYk3DFLeYUxc7Ryfz3lBhHS3bfsSAh8JfxrWTqpCPyoOlnsCiReUQ5tUzCbd6aSxxTkcWxbneM36JGe2rqU8Km-tY7Ynk';
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
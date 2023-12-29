<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\Tvdb\Api\ApiQuery\ShowTitle;
use App\Security\TvdbTokenProvider;
use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class TvdbQueryClient extends TvdbClientBase
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly TvdbTokenProvider $tokenProvider
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
                        'Authorization' => 'Bearer ' . $this->tokenProvider->getToken()
                    ]
                ]
            );
        } catch (TransportExceptionInterface $e) {
            throw new RuntimeException('Error while searching for show', 0, $e);
        }
    }

    public function seriesExtended(string $seriesId): ResponseInterface
    {
        try {
            return $this->httpClient->request(
                'GET',
                self::BASE_URL . 'series/' . $seriesId . '/extended',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->tokenProvider->getToken()
                    ]
                ]
            );
        } catch (TransportExceptionInterface $e) {
            throw new RuntimeException('Error while getting series', 0, $e);
        }
    }

    public function seasonExtended(string $seasonId): ResponseInterface
    {
        try {
            return $this->httpClient->request(
                'GET',
                self::BASE_URL . 'seasons/' . $seasonId . '/extended',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->tokenProvider->getToken()
                    ]
                ]
            );
        } catch (TransportExceptionInterface $e) {
            throw new RuntimeException('Error while getting season data', 0, $e);
        }
    }
}

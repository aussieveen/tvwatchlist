<?php

namespace App\Api;

use http\Exception\RuntimeException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class TvdbAuthClient extends TvdbClientBase
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $apikey,
        private readonly string $pin
    ) {
    }

    public function login(): ResponseInterface
    {
        try {
            return $this->httpClient->request(
                'POST',
                self::BASE_URL . 'login',
                [
                    'json' => [
                        'apikey' => $this->apikey,
                        'pin' => $this->pin
                    ]
                ]
            );
        } catch (TransportExceptionInterface $e) {
            throw new RuntimeException('Error while logging in', 0, $e);
        }
    }
}

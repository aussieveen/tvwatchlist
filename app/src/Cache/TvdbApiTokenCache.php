<?php

declare(strict_types=1);

namespace App\Cache;

use App\Api\TvdbAuthClient;
use RuntimeException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class TvdbApiTokenCache
{
    public function __construct(
        private readonly CacheInterface $cache,
        private readonly TvdbAuthClient $tvdbClient
    ) {
    }

    public function getToken(): string
    {
        return $this->cache->get('tvdb_token', function (ItemInterface $item) {
            $item->expiresAfter(60 * 60 * 24 * 7); // 7 days

            $response = $this->tvdbClient->login();
            try {
                $data = $response->toArray();
            } catch (ClientExceptionInterface|RedirectionExceptionInterface|DecodingExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
                throw new RuntimeException('Error while getting token', 0, $e);
            }
            return $data['data']['token'];
        });
    }
}

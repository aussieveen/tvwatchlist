<?php

namespace App\Entity\Tvdb\Data\Ingest;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class CriteriaFactory
{
    public function __construct(private RequestStack $requestStack)
    {
    }

    public function build(): Criteria
    {
        $request = $this->requestStack->getCurrentRequest() ?? throw new BadRequestException('No request found');
        $requestBody = json_decode($request->getContent(), true);
        if(!isset($requestBody['seriesId'])) {
            throw new BadRequestException('seriesId is required');
        }
        return new Criteria(
            $requestBody['seriesId'],
            $requestBody['season'] ?? 1,
            $requestBody['episode'] ?? 1,
            $requestBody['platform'] ?? 'Plex',
            strtolower($requestBody['universe']) ?? ''
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Entity\Tvdb\Api\ApiQuery;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class ShowTitleFactory
{
    public function __construct()
    {
    }

    public function buildFromRequestStack(RequestStack $requestStack): ShowTitle
    {
        $request = $requestStack->getCurrentRequest() ?? throw new BadRequestException('No request found');
        $searchTerm = $request->query->get('showTitle');
        if ($searchTerm === null) {
            throw new BadRequestException('No show title provided');
        }
        return new ShowTitle($searchTerm);
    }
}

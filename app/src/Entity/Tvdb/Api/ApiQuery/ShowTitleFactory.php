<?php

namespace App\Entity\Tvdb\Api\ApiQuery;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\RequestStack;

class ShowTitleFactory
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function build(): ShowTitle
    {
        $request = $this->requestStack->getCurrentRequest() ?? throw new BadRequestException('No request found');
        $searchTerm = $request->query->get('showTitle');
        if($searchTerm === null) {
            throw new BadRequestException('No show title provided');
        }
        return new ShowTitle($searchTerm);
    }
}
<?php

namespace App\Controller\Tvdb;

use App\Controller\Tvdb\Data\IngestProcess;
use App\Entity\Tvdb\Data\Ingest\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class IngestRequest extends AbstractController
{
    public function __construct(
        private readonly Criteria $criteria,
        private readonly IngestProcess $ingestProcess
    )
    {
    }

    #[Route(
        '/api/tvdb/series/ingest',
        name: 'api_tvdb_series_ingest',
        methods: ['POST']
    )]
    public function handle(): JsonResponse
    {
        $this->ingestProcess->ingest(
            $this->criteria
        );

        return new JsonResponse([
            'message' => sprintf(
                'Processing started for series: %s from Season: %d, Episode:%d',
                $this->criteria->seriesId,
                $this->criteria->season,
                $this->criteria->episode
                ),
            'status' => 202,
            'title' => 'OK'
        ]);
    }
}

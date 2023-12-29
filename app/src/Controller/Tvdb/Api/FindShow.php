<?php

declare(strict_types=1);

namespace App\Controller\Tvdb\Api;

use App\Api\TvdbQueryClient;
use App\Entity\Tvdb\Api\ApiQuery;
use App\Entity\Tvdb\Api\ShowFactory;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class FindShow extends AbstractController
{
    public function __construct(
        private readonly ApiQuery $apiQuery,
        private readonly TvdbQueryClient $tvdbClient,
        private readonly ShowFactory $showFactory
    ) {
    }

    #[Route('/api/tvdb/search', name: 'api_tvdb_search', methods: ['GET'])]
    public function find(): JsonResponse
    {
        $searchResults = $this->tvdbClient->search($this->apiQuery->showTitle);
        try {
            $body = json_decode($searchResults->getContent(), true);
            if ($body['status'] !== 'success') {
                throw new Exception('Something went wrong');
            }

            $shows = [];

            foreach ($body['data'] as $show) {
                $shows[] = $this->showFactory->create($show);
            }

            return new JsonResponse([
                'status' => 200,
                'title' => 'OK',
                'data' => array_values(array_filter($shows))
            ]);
        } catch (Exception $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
                'status' => 500,
                'title' => 'Something went wrong'
            ]);
        }
    }
}

<?php

namespace App\Controller;

use App\Adapter\QueryAdapter;
use App\Entity\Search\ApiQuery;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NextUpController extends AbstractController
{
    public function __construct(
        private readonly PaginatedFinderInterface $finder,
        private readonly QueryAdapter $queryAdapter
    )
    {
    }

    #[Route('/api/nextup', name: 'next_up')]
    public function search(ApiQuery $apiQuery):Response
    {
        try{
            $elasticQuery = $this->queryAdapter->adapt($apiQuery);
        }catch (BadRequestException $e){
            return new JsonResponse([
                'message' => $e->getMessage(),
                'status' => Response::HTTP_BAD_REQUEST,
                'title' => Response::$statusTexts[Response::HTTP_BAD_REQUEST]
            ], Response::HTTP_BAD_REQUEST);
        }

        $paginator = $this->finder->createHybridPaginatorAdapter(
            $elasticQuery
        );

        $response = $paginator->getResults(
            0,
            30
        )->toArray();

        $results = [];
        foreach($response as $doc){
            $transDoc = $doc->getTransformed();
            $results[] = $transDoc;
        }

        return $this->json($results);
    }
}

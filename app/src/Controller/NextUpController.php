<?php

namespace App\Controller;

use App\Adapter\QueryAdapter;
use App\Document\Episode;
use App\Document\History;
use App\Entity\Search\ApiQuery;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NextUpController extends AbstractController
{
    private DocumentManager $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    #[Route('/api/nextup', name: 'next_up')]
    public function search():Response
    {
        $builder = $this->documentManager->createQueryBuilder(Episode::class)
            ->field('showTitle')->equals($this->getRandomShow())
            ->field('watched')->equals(false)
            ->sort('season', 'ASC')
            ->sort('episode', 'ASC')
            ->limit(1);
        $episode = $builder->getQuery()->execute();
        return $this->json($episode->toArray());
    }

    private function getRecentlyWatched(): array
    {
        $builder = $this->documentManager->createAggregationBuilder(History::class);
        $builder->sort('id', 'DESC')->limit(5);
        $results = $builder->getAggregation()->getIterator()->toArray();
        $watched = [];
        foreach($results as $result){
            $watched[] = $result['showTitle'];
        }
        return array_unique($watched);
    }

    private function getRandomShow(): string
    {
        $builder = $this->documentManager->createAggregationBuilder(Episode::class);
        $builder->match()->field('watched')->equals(false)
            ->match()->field('showTitle')->notIn($this->getRecentlyWatched())
            ->group()->field('id')->expression('$showTitle');
        $showList = $builder->getAggregation()->getIterator()->toArray();
        return $showList[array_rand($showList)]['_id'];
    }
}

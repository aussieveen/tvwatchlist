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
    public function __construct(private readonly DocumentManager $documentManager)
    {
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
        $showList = [];
        foreach($this->getUniverses() as $universe){
            $builder = $this->documentManager->createAggregationBuilder(Episode::class);
            $builder->match()->field('watched')->equals(false)
                ->match()->field('universe')->equals($universe)
                ->sort('airDate', 'ASC')
                ->limit(1);
            foreach($builder->getAggregation()->getIterator()->toArray() as $show){
                $showList[] = $show['showTitle'];
            }
        }

        $builder = $this->documentManager->createAggregationBuilder(Episode::class);
        $builder->match()->field('watched')->equals(false)
            ->match()->field('showTitle')->notIn($this->getRecentlyWatched())
            ->match()->field('universe')->equals('')
            ->group()->field('id')->expression('$showTitle');
        foreach($builder->getAggregation()->getIterator()->toArray() as $show){
            $showList[] = $show['_id'];
        }
        if (empty($showList)){
            return '';
        }
        return $showList[array_rand($showList)];
    }

    private function getUniverses(): array
    {
        $builder = $this->documentManager->createAggregationBuilder(Episode::class);
        $builder->match()->field('watched')->equals(false)
            ->match()->field('universe')->notEqual('')
            ->group()->field('id')->expression('$universe');
        $universeList = [];
        foreach ($builder->getAggregation()->getIterator()->toArray() as $key => $universe){
            $universeList[] = $universe['_id'];
        }
        return $universeList;
    }

}

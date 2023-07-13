<?php

namespace App\Controller\Site;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IngestPageController extends AbstractController
{
    #[Route('/ingest', name: 'ingest_page')]
    public function run(): Response
    {
        return $this->render('ingest.html.twig');
    }
}
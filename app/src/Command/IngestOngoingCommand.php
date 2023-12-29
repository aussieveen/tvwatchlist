<?php

declare(strict_types=1);

namespace App\Command;

use App\Controller\Tvdb\Data\IngestProcess;
use App\Entity\Tvdb\Data\Ingest\Criteria;
use App\Entity\Tvdb\Data\Ingest\CriteriaFactory;
use App\Repository\Episode;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:ingest-ongoing',
    description: 'Ingest ongoing series',
)]
class IngestOngoingCommand extends Command
{
    public function __construct(
        private readonly Episode $episodeRepository,
        private readonly IngestProcess $ingestProcess
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //Find all series that are currently airing
        $ongoingSeries = $this->episodeRepository->getUnfinishedSeries();

        //Reingest the series from TVDB
        foreach ($ongoingSeries as $series) {
            //Get all existing episodes for the series
            $firstIngestedEpisode = $this->episodeRepository->getFirstEpisodeForSeries($series['_id']);
            $criteria = new Criteria(
                $firstIngestedEpisode->tvdbSeriesId,
                $firstIngestedEpisode->season,
                $firstIngestedEpisode->episode,
                $firstIngestedEpisode->platform,
                $firstIngestedEpisode->universe
            );
            //Ingest the series
            $this->ingestProcess->ingest($criteria);
        }

        return Command::SUCCESS;
    }
}

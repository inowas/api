<?php

declare(strict_types=1);

namespace App\Command;

use App\Model\Common\Projection;
use App\Model\User\Projector\UserProjector;
use App\Service\AggregateRepository;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ReprojectCommand extends Command
{

    protected static $defaultName = 'app:reproject';

    protected $aggregateRepository;
    protected $projections;

    public function __construct(AggregateRepository $aggregateRepository, UserProjector $userProjection)
    {
        $this->aggregateRepository = $aggregateRepository;
        $this->projections[] = $userProjection;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Reprojects selected projections.')
            ->addArgument('aggregateName', InputArgument::OPTIONAL, 'Username')
            ->setHelp('This command allows you to recreate your projections.')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $aggregateName = $input->getArgument('aggregateName');

        if (!$aggregateName) {
            $output->writeln('The following projections are available:');
            $output->writeln('');

            /**
             * @var Projection $projection
             */
            foreach ($this->projections as $key => $projection) {
                $output->writeln(sprintf('%d: %s', $key+1, $projection->aggregateName()));
            }

            return;
        }

        /**
         * @var Projection $projection
         */
        foreach ($this->projections as $key => $projection) {
            if ($key+1 == $aggregateName || $projection->aggregateName() === $aggregateName) {
                $events = $this->aggregateRepository->getEventsByAggregateName($projection->aggregateName());
                $projection->recreateFromHistory($events);
                $output->writeln(sprintf('Projection %s successfully recreated with %d events.', $projection->aggregateName(), count($events)));
                return;
            }
        }

        $output->writeln(sprintf('Projection %s not found.', $aggregateName));
    }
}

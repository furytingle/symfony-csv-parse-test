<?php

declare(strict_types=1);

namespace App\Commands;

use App\Services\Import\ImportProductsServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportProductsCommand extends Command
{
    protected static $defaultDescription = 'Imports products from given csv file';

    protected static $defaultName = 'product:import';

    public function __construct(private ImportProductsServiceInterface $importProductsService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('path', InputArgument::REQUIRED, 'Path to a csv file');
        $this->addOption(
            'test',
            't',
            InputOption::VALUE_NONE,
            'Test mode execution, without actual DB insert'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $input->getArgument('path');
        $test = $input->getOption('test');

        $this->importProductsService->importFromCSV($path, $test);

        $output->writeln('<info>Finished !</info>');

        $processed = $this->importProductsService->getRowsProcessed();
        $output->writeln("<info>Processed: $processed</info>");

        $filtered = $this->importProductsService->getRowsFiltered();
        $output->writeln("<comment>Did not meet conditions: $filtered</comment>");

        $invalid = $this->importProductsService->getRowsInvalid();
        $output->writeln("<error>Invalid: $invalid</error>");

        return Command::SUCCESS;
    }
}
<?php

namespace MageConfigSync\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DumpCommand extends BaseCommand
{
    /**
     * @inheritdoc
     */
    const NAME = 'dump';
    
    /**
     * @inheritdoc
     */
    const DESCRIPTION = 'Output the current configuration.';
    
    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return ExitCode::SUCCESS;
    }
}
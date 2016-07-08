<?php

namespace MageConfigSync\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DiffCommand extends BaseCommand
{
    /**
     * @inheritdoc
     */
    const NAME = 'diff';
    
    /**
     * @inheritdoc
     */
    const DESCRIPTION = 'Compare the current Magento configuration with a file.';
    
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        parent::configure();
        
        $this
            ->addArgument(
                'config-yaml-file',
                InputArgument::REQUIRED,
                'The YAML file containing the configuration settings.'
            );
    }
    
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
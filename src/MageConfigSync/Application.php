<?php

namespace MageConfigSync;

use MageConfigSync\Command\DiffCommand;
use MageConfigSync\Command\DumpCommand;
use MageConfigSync\Command\LoadCommand;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application as ConsoleApplication;

class Application extends ConsoleApplication
{
    const CLI_NAME = 'mageconfigsync';
    const CLI_VERSION = '1.0.0-dev';
    
    /**
     * Application constructor.
     *
     * @param ContainerInterface $container
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct(self::CLI_NAME, self::CLI_VERSION);
        
        $this->add($container->get(LoadCommand::class));
        $this->add($container->get(DumpCommand::class));
        $this->add($container->get(DiffCommand::class));
    }
}

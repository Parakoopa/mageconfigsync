<?php

namespace MageConfigSync\Command;

use DI\Container;
use MageConfigSync\Util\Logger;
use Monolog\Logger as MonologLogger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Monolog\Handler\ConsoleHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseCommand extends Command
{
    use Logger;
    
    /**
     * The location that we should start looking for the Magento root directory.
     */
    const OPTION_MAGE_ROOT = 'magento-root';

    /**
     * The environment we want to use.
     */
    const OPTION_ENV = 'env';
    
    /**
     * The name of the command.
     */
    const NAME = null;
    
    /**
     * The description of the command.
     */
    const DESCRIPTION = null;
    
    /**
     * @var Container
     */
    protected $container;
    
    
    public function __construct(Container $container)
    {
        parent::__construct(null);
        
        $this->container = $container;
        
        $this->setLogger($this->container->get(LoggerInterface::class));
    }
    
    /**
     * Configure the name, description and options of the command.
     *
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure()
    {
        parent::configure();
    
        $this->configureName();
        $this->configureDescription();
        $this->configureDefaultOptions();
    }
    
    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->configureLoggerVerbosity($output);
    }
    
    /**
     * Set default options that are shared by all on the command.
     */
    protected function configureDefaultOptions()
    {
        $this->addOption(
            self::OPTION_MAGE_ROOT,
            null,
            InputArgument::OPTIONAL,
            'The Magento root directory, defaults to current working directory.',
            getcwd()
        );
            
        $this->addOption(
            self::OPTION_ENV,
            null,
            InputArgument::OPTIONAL,
            'Environment to import.  If one is not provided, no environment will be used.'
        );
    }
    
    /**
     * Set the name of the command.
     *
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configureName()
    {
        $this->setName(
            (static::NAME !== null) ? static::NAME : static::class
        );
    }
    
    /**
     * Set the description of the command.
     */
    protected function configureDescription()
    {
        $this->setDescription(
            (static::DESCRIPTION !== null) ? static::DESCRIPTION : 'No description provided for this command.'
        );
    }
    
    /**
     * @param OutputInterface $output
     */
    protected function configureLoggerVerbosity(OutputInterface $output)
    {
        if ($this->logger instanceof MonologLogger) {
            $consoleHandler = new ConsoleHandler($output);
            $this->logger->pushHandler($consoleHandler);
        } else {
            $this->logger->warning("Couldn't add Symfony\\Console handler as the logger isn't Monolog");
        }
    }
}
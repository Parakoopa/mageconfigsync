<?php

namespace MageConfigSync\Command;

use Aura\Sql\ExtendedPdo;
use DI\Container;
use MageConfigSync\Config\Environment;
use MageConfigSync\ConfigReader\ConfigReader;
use MageConfigSync\Framework\Magento;
use MageConfigSync\Parser\YamlParser;
use MageConfigSync\Util\ArrayUtil;
use Meanbee\LibMageConf\RootDiscovery;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Exception\ParseException;

class LoadCommand extends BaseCommand
{
    const ARG_FILE_NAME = 'config-yaml-file';
    /**
     * @inheritdoc
     */
    const NAME = 'load';
    
    /**
     * @inheritdoc
     */
    const DESCRIPTION = 'Import configuration from a file into Magento.';

    /**
     * @var YamlParser
     */
    protected $yamlParser;
    
    /**
     * @var ConfigReader
     */
    protected $configReader;
    
    public function __construct(Container $container, YamlParser $yamlParser, ConfigReader $configReader)
    {
        parent::__construct($container);
        
        $this->yamlParser = $yamlParser;
        $this->configReader = $configReader;
    }
    
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        parent::configure();
        
        $this
            ->addArgument(
                self::ARG_FILE_NAME,
                InputArgument::REQUIRED,
                'The YAML file containing the configuration settings.'
            );
    }
    
    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \DI\NotFoundException
     * @throws \DI\DependencyException
     * @throws \InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        
        $fileName     = $input->getArgument(self::ARG_FILE_NAME);
        $requestedEnv = $input->getOption(self::OPTION_ENV) ?: null;
        
        $parser       = $this->yamlParser;
        $configReader = $this->configReader;

        try {
            $parsedFile = $parser->parse($fileName);
        } catch (\Exception $e) {
            $this->error('Error experienced parsing YAML file: '. $e->getMessage());
            return ExitCode::GENERIC_ERROR;
        }

        $fileEnvironments = $configReader->generate($parsedFile);
        
        if (null === $requestedEnv && count($fileEnvironments) > 1) {
            $this->error('File contains multiple environments but no environment specified.');
            return ExitCode::GENERIC_ERROR;
        }
        
        if (null !== $requestedEnv) {
            $environment = ArrayUtil::findValueByCallback($fileEnvironments, function ($item) use ($requestedEnv) {
                /** @var Environment $item */
                return $item->getName() === $requestedEnv;
            });
            
            if (!$environment) {
                $this->error(sprintf(
                    "Requested environment '%s' was not found in the file.",
                    $requestedEnv
                ));
                
                return ExitCode::GENERIC_ERROR;
            }
        } else {
            $environment = $fileEnvironments[0];
        }
        
        /**
         * @var $rootDiscover RootDiscovery
         */
        $rootDiscover = $this->container->make(RootDiscovery::class, [
            $input->hasOption(self::OPTION_MAGE_ROOT) ? $input->getOption(self::OPTION_MAGE_ROOT) : getcwd()
        ]);
        
        $configReader = $rootDiscover->getConfigReader();
        
        $pdo = new ExtendedPdo(
            sprintf(
                'mysql:dbname=%s;host=%s;port=%s',
                $configReader->getDatabaseName(),
                $configReader->getDatabaseHost(),
                $configReader->getDatabasePort()
            ),
            $configReader->getDatabaseUsername(),
            $configReader->getDatabasePassword()
        );
        
        $magento = new Magento($pdo);
        
        $configItemSet  = $environment->getConfigItemSet();
        $processedItems = $magento->processConfigItemSet($configItemSet);
        
        $processedConfigCount = count($processedItems);
        
        foreach ($processedItems as $item) {
            if ($item->isDelete()) {
                $output->writeln(sprintf(
                    '<info>%s: %s -> (deleted)</info>',
                    $item->getScope(),
                    $item->getKey()
                ));
            } else {
                $output->writeln(sprintf(
                    '<info>%s: %s -> %s</info>',
                    $item->getScope(),
                    $item->getKey(),
                    $item->getValue()
                ));
            }
        }
        
        $countRequestedChanges = count($configItemSet);
        $countChangesMade = count($processedConfigCount);
        
        if ($countRequestedChanges !== $countChangesMade) {
            $output->writeln(sprintf(
                'Found %d config%s to apply but only %d needed to be applied.',
                $countRequestedChanges,
                ($countChangesMade == 1) ? '' : 's',
                $countChangesMade
            ));
        } else {
            $output->writeln(sprintf(
                'Found %d config%s to apply, all of which were applied successfully.',
                $countRequestedChanges,
                ($countChangesMade == 1) ? '' : 's'
            ));
        }
        
        return ExitCode::SUCCESS;
    }
}
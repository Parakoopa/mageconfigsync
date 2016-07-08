<?php

namespace MageConfigSync\ConfigReader;

use MageConfigSync\Config\ConfigItem;
use MageConfigSync\Config\ConfigItemSet;
use MageConfigSync\Config\Environment;
use MageConfigSync\Util\ArrayUtil;
use MageConfigSync\Util\Logger;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

/**
 * Class OriginalReader
 *
 * The purpose of a reader is to make sense of an input array from a Parser and product environments.
 *
 * @package MageConfigSync\ConfigReader
 */
class ConfigReader
{
    use Logger;
    
    public function __construct(LoggerInterface $logger)
    {
        $this->setLogger($logger);
    }
    
    /**
     * @param array $input
     * @return Environment[]
     */
    public function generate(array $input)
    {
        $environments = [];
        
        if ($this->containsMultipleEnvironments($input)) {
            $this->info('Input contains multiple environments');
            
            foreach ($input as $environment => $scopes) {
                $this->info(sprintf('Identified environment: %s', $environment));
    
                if (is_array($scopes)) {
                    $configItemSet = $this->generateConfigItemSet($scopes);
                    $environments[] = new Environment($environment, $configItemSet);
                }
            }
        } else {
            $this->info('Input contains a single environment');
    
            $configItemSet = $this->generateConfigItemSet($input);
            
            $environments[] = new Environment('default', $configItemSet);
        }
        
        $this->info(sprintf('Identified a total of %d environments', count($environments)));
        
        return $environments;
    }
    
    /**
     * @param array $input
     * @return bool
     */
    protected function containsMultipleEnvironments(array $input)
    {
        return ArrayUtil::depth($input) > 2;
    }
    
    /**
     * @param array $input
     * @return ConfigItemSet
     */
    protected function generateConfigItemSet(array $input)
    {
        $configItemSet = new ConfigItemSet();
        
        foreach ($input as $scope => $keys) {
            if (is_array($keys)) {
                foreach ($keys as $key => $value) {
                    $configItemSet->add(
                        new ConfigItem($scope, $key, $value)
                    );
                }
            }
        }
        
        return $configItemSet;
    }
}
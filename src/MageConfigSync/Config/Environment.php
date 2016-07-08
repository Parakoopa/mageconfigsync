<?php

namespace MageConfigSync\Config;

class Environment
{
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var ConfigItemSet
     */
    private $configItemSet;
    
    public function __construct($name, ConfigItemSet $configItemSet)
    {
        $this->name = $name;
        $this->configItemSet = $configItemSet;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * @return ConfigItemSet
     */
    public function getConfigItemSet()
    {
        return $this->configItemSet;
    }
}
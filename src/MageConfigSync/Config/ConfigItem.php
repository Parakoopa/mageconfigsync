<?php

namespace MageConfigSync\Config;

class ConfigItem
{
    private $key;
    private $value;
    private $scope;
    
    public function __construct($scope, $key, $value)
    {
        $this->key   = $key;
        $this->value = $value;
        $this->scope = $scope;
    }
    
    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }
    
    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * @return mixed
     */
    public function getScope()
    {
        return $this->scope;
    }
    
    /**
     * @return bool
     */
    public function isDelete()
    {
        return $this->getValue() === null;
    }
}

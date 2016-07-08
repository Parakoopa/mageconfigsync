<?php

namespace MageConfigSync\Config\ConfigItemSet;

use MageConfigSync\Config\ConfigItem;
use MageConfigSync\Config\ConfigItemSet;

abstract class Processor implements Consumer, Producer
{
    /**
     * @param $scope
     * @param $key
     * @param $getValue
     * @return int Affected rows
     */
    abstract protected function deleteConfig($scope, $key);
    
    /**
     * @param $scope
     * @param $key
     * @param $value
     * @return int Affected rows
     */
    abstract protected function upsertConfig($scope, $key, $value);
    
    /**
     * @param ConfigItemSet $configItemSet
     * @return ConfigItemSet $processedItems
     */
    public function processConfigItemSet(ConfigItemSet $configItemSet)
    {
        $processedConfigItems = new ConfigItemSet();
        
        $deleteActions = [];
        $upsertActions = [];
        
        foreach ($configItemSet as $configItem)
        {
            if ($configItem->isDelete()) {
                $deleteActions[] = $configItem;
            } else {
                $upsertActions[] = $configItem;
            }
        }
        
        $successfulDeletions = $this->processDeletions($deleteActions);
        $successfulUpserts   = $this->processUpserts($upsertActions);
        
        $processedConfigItems
            ->addMultiple($successfulDeletions)
            ->addMultiple($successfulUpserts);
        
        return $processedConfigItems;
    }
    
    /**
     * @param ConfigItem[] $deleteActions
     * @return ConfigItem[] Items that were successful
     */
    private function processDeletions($deleteActions)
    {
        $successful = [];
        
        foreach ($deleteActions as $configItem)
        {
            $successfulDeletion = $this->deleteConfig($configItem->getScope(), $configItem->getKey());
            
            if ($successfulDeletion) {
                $successful[] = $configItem;
            }
        }
        
        return $successful;
    }
    
    /**
     * @param ConfigItem[] $upsertActions
     * @return ConfigItem[] Items that were successful
     */
    private function processUpserts($upsertActions)
    {
        $successful = [];
        
        foreach ($upsertActions as $configItem)
        {
            $successfulUpsert = $this->upsertConfig($configItem->getScope(), $configItem->getKey(), $configItem->getValue());
            
            if ($successfulUpsert) {
                $successful[] = $configItem;
            }
        }
        
        return $successful;
    }
}
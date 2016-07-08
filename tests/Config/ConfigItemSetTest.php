<?php

namespace MageConfigSyncTest\Config;

use MageConfigSync\Config\ConfigItem;
use MageConfigSync\Config\ConfigItemSet;

class ConfigItemSetTest extends \PHPUnit_Framework_TestCase
{
    public function test_countable()
    {
        $configSet = new ConfigItemSet();

        $this->assertCount(0, $configSet);

        $configSet->add($this->makeConfigItem());
        $configSet->add($this->makeConfigItem());

        $this->assertCount(2, $configSet);

        $configSet->add($this->makeConfigItem());

        $this->assertCount(3, $configSet);

        $configSet->add($this->makeConfigItem());
        $configSet->add($this->makeConfigItem());

        $this->assertCount(5, $configSet);
        
        $configSet->addMultiple([
            $this->makeConfigItem(),
            $this->makeConfigItem()
        ]);
    
        $this->assertCount(7, $configSet);
    }

    protected function makeConfigItem()
    {
        return new ConfigItem("default", "hello", "world");
    }
}
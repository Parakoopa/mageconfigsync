<?php


namespace MageConfigSyncTest\Config;

use MageConfigSync\Config\ConfigItem;

class ConfigItemTest extends \PHPUnit_Framework_TestCase
{
    public function test_accessors()
    {
        $item = new ConfigItem("default", "hello", "world");

        $this->assertEquals("default", $item->getScope());
        $this->assertEquals("hello", $item->getKey());
        $this->assertEquals("world", $item->getValue());
    }
}
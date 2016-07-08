<?php

namespace MageConfigSyncTest\ConfigReader;

use MageConfigSync\Config\ConfigItemSet;
use MageConfigSync\ConfigReader\ConfigReader;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class ConfigReaderTest extends TestCase
{
    public function test_generates_config_item_set()
    {
        $reader = new ConfigReader(new NullLogger());
        $environments = $reader->generate([
            'default' => [
                '1/2/3' => 4,
                '4/5/6' => null
            ]
        ]);
        
        $this->assertCount(1, $environments);
        
        $environment = $environments[0];
        $configItemSet = $environment->getConfigItemSet();
        
        $this->assertInstanceOf(ConfigItemSet::class, $configItemSet);
        $this->assertCount(2, $configItemSet);
        
        $configItemOne = $configItemSet[0];
        $configItemTwo = $configItemSet[1];
        
        if ($configItemOne->getKey() !== '1/2/3') {
            $tmp = $configItemOne;
            $configItemOne = $configItemTwo;
            $configItemTwo = $tmp;
            unset($tmp);
        }
        
        $this->assertEquals('1/2/3', $configItemOne->getKey());
        $this->assertEquals(4, $configItemOne->getValue());
        $this->assertEquals('default', $configItemOne->getScope());
        $this->assertFalse($configItemOne->isDelete());
        
        $this->assertEquals('4/5/6', $configItemTwo->getKey());
        $this->assertEquals(null, $configItemTwo->getValue());
        $this->assertEquals('default', $configItemTwo->getScope());
        $this->assertTrue($configItemTwo->isDelete());
    }
    
    public function test_generates_multiple_environments()
    {
        $reader = new ConfigReader(new NullLogger());
        $environments = $reader->generate([
            'development' => [
                'default' => [
                    'a/b/c' => 123
                ],
                'stores-1' => [
                    'd/e/f' => 456
                ]
            ],
            'production' => [
                'default' => [
                    'a/b/c' => 456
                ]
            ]
        ]);
    
        $this->assertCount(2, $environments);
        
        $devEnv = $environments[0];
        $prodEnv = $environments[1];
        
        if ($devEnv->getName() !== 'development') {
            $tmp = $devEnv;
            $devEnv = $prodEnv;
            $prodEnv = $tmp;
            unset($tmp);
        }
        
        $this->assertEquals('development', $devEnv->getName());
        $this->assertEquals('production', $prodEnv->getName());
        
        $this->assertInstanceOf(ConfigItemSet::class, $devEnv->getConfigItemSet());
        $this->assertInstanceOf(ConfigItemSet::class, $prodEnv->getConfigItemSet());
        
        $devConfigSet = $devEnv->getConfigItemSet();
        $prodConfigSet = $prodEnv->getConfigItemSet();
        
        $this->assertCount(1, $prodConfigSet);
        $this->assertCount(2, $devConfigSet);
    }
}
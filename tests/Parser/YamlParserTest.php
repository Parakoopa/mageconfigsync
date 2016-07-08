<?php

namespace MageConfigSyncTest\Parser;

use MageConfigSync\Parser\YamlParser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Parser;

class YamlParserTest extends TestCase
{
    public function test_reading_yaml_file_with_no_environment()
    {
        $parser = new YamlParser(new Parser());
        $result = $parser->parse($this->getFixtureFile('noEnvironment'));
        
        self::assertInternalType('array', $result);
        self::assertCount(1, $result);
        self::assertArrayHasKey('default', $result);
        
        self::assertCount(2, $result['default']);
        self::assertArrayHasKey('currency/options/base', $result['default']);
        self::assertArrayHasKey('dev/restrict/allow_ips', $result['default']);
        
        self::assertEquals('GBP', $result['default']['currency/options/base']);
        self::assertNull($result['default']['dev/restrict/allow_ips']);
    }
    
    public function test_reading_yaml_file_with_environment()
    {
        $parser = new YamlParser(new Parser());
        $result = $parser->parse($this->getFixtureFile('withEnvironments'));
        
        self::assertInternalType('array', $result);
        self::assertCount(2, $result);
        self::assertArrayHasKey('production', $result);
        self::assertArrayHasKey('development', $result);
        
        self::assertCount(2, $result['development']);
        self::assertArrayHasKey('default', $result['development']);
        self::assertArrayHasKey('stores-1', $result['development']);
    
        self::assertCount(1, $result['development']['default']);
        self::assertArrayHasKey('dev/debug/template_hints', $result['development']['default']);
        self::assertEquals(1, $result['development']['default']['dev/debug/template_hints']);
    
        self::assertCount(2, $result['development']['stores-1']);
        self::assertArrayHasKey('currency/options/base', $result['development']['stores-1']);
        self::assertArrayHasKey('dev/restrict/allow_ips', $result['development']['stores-1']);
        
        self::assertEquals('GBP', $result['development']['stores-1']['currency/options/base']);
        self::assertNull($result['development']['stores-1']['dev/restrict/allow_ips']);
        
        self::assertCount(1, $result['production']);
        self::assertArrayHasKey('default', $result['production']);
        
        self::assertCount(1, $result['production']['default']);
        self::assertArrayHasKey('dev/debug/template_hints', $result['production']['default']);
        self::assertEquals(0, $result['production']['default']['dev/debug/template_hints']);
    }
    
    /**
     * Return the filename of a fixture.
     *
     * @param $name
     * @return string
     */
    protected function getFixtureFile($name)
    {
        $fileName = implode(DIRECTORY_SEPARATOR, [
            __DIR__,
            '..',
            'fixtures',
            $name . '.yml'
        ]);
        
        if (!file_exists($fileName)) {
            self::fail(sprintf("Fixture '%s' does not exist", $fileName));
        }
    
        if (!is_readable($fileName)) {
            self::fail(sprintf("Fixture '%s' exists but is not readable", $fileName));
        }
        
        return $fileName;
    }
}
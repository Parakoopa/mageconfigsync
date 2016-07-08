<?php

namespace MageConfigSync\Parser;

use Symfony\Component\Yaml\Parser;

/**
 * Class YamlParser
 *
 * The purpose of a parser is to consume a file and return an array representation of that file.
 *
 * @package MageConfigSync\Parser
 */
class YamlParser
{
    /** @var Parser */
    private $yaml;
    
    /**
     * YamlParser constructor.
     *
     * @param Parser|null $yaml
     */
    public function __construct(Parser $yaml)
    {
        $this->yaml = $yaml;
    }
    
    /**
     * @param $file
     * @return mixed
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     * @throws \InvalidArgumentException
     */
    public function parse($file)
    {
        if (!file_exists($file)) {
            throw new \InvalidArgumentException(sprintf("File '%s' does not exist.", $file));
        }
    
        if (!is_readable($file)) {
            throw new \InvalidArgumentException(sprintf("File '%s' exists but is not readable.", $file));
        }
        
        return $this->yaml->parse(file_get_contents($file));
    }
}
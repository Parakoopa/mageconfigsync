<?php

namespace MageConfigSync\Config;

use ArrayAccess;
use Countable;
use Iterator;

class ConfigItemSet implements Countable, Iterator, ArrayAccess
{
    /**
     * @var ConfigItem[]
     */
    private $items = [];
    private $position = 0;

    /**
     * @param ConfigItem $configItem
     * @return $this
     */
    public function add(ConfigItem $configItem)
    {
        $this->items[] = $configItem;

        return $this;
    }
    
    /**
     * @param array $configItems
     * @return $this
     */
    public function addMultiple(array $configItems)
    {
        array_walk($configItems, function ($item) {
            $this->add($item);
        });
        
        return $this;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * @return ConfigItem
     */
    public function current()
    {
        return $this->items[$this->key()];
    }

    public function next()
    {
        $this->position++;
    }

    public function key()
    {
        return $this->position;
    }

    public function valid()
    {
        return isset($this->items[$this->position]);
    }

    public function rewind()
    {
        $this->position = 0;
    }
    
    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }
    
    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }
    
    public function offsetSet($offset, $value)
    {
        $this->items[$offset] = $value;
    }
    
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }
}

<?php

namespace MageConfigSyncTest\Util;

use MageConfigSync\Util\ArrayUtil;
use PHPUnit\Framework\TestCase;

class ArrayUtilTest extends TestCase
{
    public function test_depth()
    {
        $this->assertEquals(1, ArrayUtil::depth([]));
        $this->assertEquals(1, ArrayUtil::depth(['a' => 'a']));
    
        $this->assertEquals(2, ArrayUtil::depth(['a' => ['a']]));
    
        $this->assertEquals(2, ArrayUtil::depth(['a' => [
            'a' => 'a'
        ]]));
    
        $this->assertEquals(2, ArrayUtil::depth(['a' => [
            'a' => 'a',
            'b' => 'b'
        ]]));
        
        $this->assertEquals(3, ArrayUtil::depth(['a' => [
            'a' => [
                'a' => 'a'
            ],
            'b' => [
                'b' => 'b'
            ]
        ]]));
    
        $this->assertEquals(3, ArrayUtil::depth(['a' => [
            'a' => [
                'a' => 'a'
            ],
            'b' => 'b'
        ]]));
        
        $this->assertEquals(4, ArrayUtil::depth(['a' => [
            'a' => [
                'a' => 'a'
            ],
            'b' => 'b',
            'c' => [
                'c' => [
                    'c'
                ]
            ]
        ]]));
    }
    
    public function test_find_by_callback()
    {
        $a = new \stdClass(); $a->a = 123;
        $b = new \stdClass(); $b->a = 456;
        $c = new \stdClass(); $c->b = 789;
        
        $input = [$a, $b, $c];

        $searchFnOne = function ($input) {
            return $input->a === 456;
        };
        
        $this->assertEquals(1, ArrayUtil::findKeyByCallback($input, $searchFnOne));
        $this->assertEquals($b, ArrayUtil::findValueByCallback($input, $searchFnOne));
        
        $searchFnNotFound = function ($input) {
            return $input->a = 789;
        };
        
        $this->assertEquals(-1, ArrayUtil::findKeyByCallback($input, $searchFnNotFound));
        $this->assertNull(ArrayUtil::findValueByCallback($input, $searchFnNotFound));
    }
}
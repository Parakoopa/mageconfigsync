<?php
namespace MageConfigSync\Util;

class ArrayUtil
{
    /**
     * http://www.php.net/manual/en/function.array-diff-assoc.php#111675
     *
     * @param $array1
     * @param $array2
     * @return array
     */
    public static function diffAssocRecursive($array1, $array2)
    {
        $difference = array();

        foreach ($array1 as $key => $value) {
            if (is_array($value)) {
                if (!isset($array2[$key]) || !is_array($array2[$key])) {
                    $difference[$key] = $value;
                } else {
                    $new_diff = self::diffAssocRecursive($value, $array2[$key]);
                    if (!empty($new_diff)) {
                        $difference[$key] = $new_diff;
                    }
                }
            } elseif (!array_key_exists($key, $array2) || $array2[$key] != $value) {
                $difference[$key] = $value;
            }
        }

        return $difference;
    }
    
    public static function depth(array $array)
    {
        $max_depth = 1;
    
        foreach ($array as $value) {
            if (is_array($value)) {
                $depth = self::depth($value) + 1;
            
                if ($depth > $max_depth) {
                    $max_depth = $depth;
                }
            }
        }
    
        return $max_depth;
    }
    
    
    /**
     * @param array    $array
     * @param callable $callback
     * @return integer
     */
    public static function findKeyByCallback(array $array, callable $callback)
    {
        foreach ($array as $idx => $item) {
            if ($callback($item) === true) {
                return $idx;
            }
        }
        
        return -1;
    }
    
    
    /**
     * @param array    $array
     * @param callable $callback
     * @return mixed
     */
    public static function findValueByCallback(array $array, callable $callback)
    {
        $idx = self::findKeyByCallback($array, $callback);
        
        if ($idx > 0) {
            return $array[$idx];
        }
        
        return null;
    }
}

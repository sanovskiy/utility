<?php
namespace Unit;

use PHPUnit\Framework\TestCase;
use Sanovskiy\Utility\Arrays;

class ArraysTest extends TestCase
{
    public function testSmartMergeSimple()
    {
        $array1 = ['a' => 1];
        $array2 = ['b' => 2];
        $result = Arrays::smartMerge($array1, $array2);
        $this->assertEquals(['a' => 1, 'b' => 2], $result);
    }

    public function testSmartMergeOverwrite()
    {
        $array1 = ['a' => 1];
        $array2 = ['a' => 2];
        $result = Arrays::smartMerge($array1, $array2);
        $this->assertEquals(['a' => 2], $result);
    }

    public function testSmartMergeRecursive()
    {
        $array1 = ['a' => ['b' => 1]];
        $array2 = ['a' => ['c' => 2]];
        $result = Arrays::smartMerge($array1, $array2);
        $this->assertEquals(['a' => ['b' => 1, 'c' => 2]], $result);
    }

    public function testSmartMergeDeepRecursive()
    {
        $array1 = ['a' => ['b' => ['c' => 1]]];
        $array2 = ['a' => ['b' => ['d' => 2]]];
        $result = Arrays::smartMerge($array1, $array2);
        $this->assertEquals(['a' => ['b' => ['c' => 1, 'd' => 2]]], $result);
    }
}
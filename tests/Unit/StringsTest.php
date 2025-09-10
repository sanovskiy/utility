<?php
namespace Unit;

use PHPUnit\Framework\TestCase;
use Sanovskiy\Utility\Strings;

class StringsTest extends TestCase
{
    // Test isURL
    public function testIsURL()
    {
        $this->assertTrue(Strings::isURL('https://example.com'));
        $this->assertTrue(Strings::isURL('http://localhost:8080'));
        $this->assertTrue(Strings::isURL('ftp://files.example.com'));

        $this->assertFalse(Strings::isURL('not a url'));
        $this->assertFalse(Strings::isURL('example.com')); // без схемы
    }

    // Test makePath
    public function testMakePath()
    {
        $this->assertEquals('path'.DIRECTORY_SEPARATOR.'to'.DIRECTORY_SEPARATOR.'file', Strings::makePath(['path', 'to', 'file']));
        $this->assertEquals('single', Strings::makePath(['single']));
        $this->assertEquals('', Strings::makePath([])); // empty array
    }

    // Test deprecated CamelCase (для обратной совместимости)
    public function testDeprecatedCamelCase()
    {
        $this->assertEquals('HelloWorld', Strings::CamelCase('hello_world', true));
        $this->assertEquals('helloWorld', Strings::CamelCase('hello-world', false)); // hyphens
        $this->assertEquals('TestString', Strings::CamelCase('test_string', true));
    }

    // Test numberCondition (русские множественные формы)
    public function testNumberCondition()
    {
        // 1 штука
        $this->assertEquals('штука', Strings::numberCondition(1, 'штук', 'штуки', 'штука'));

        // 2-4 штуки
        $this->assertEquals('штуки', Strings::numberCondition(2, 'штук', 'штуки', 'штука'));
        $this->assertEquals('штуки', Strings::numberCondition(3, 'штук', 'штуки', 'штука'));
        $this->assertEquals('штуки', Strings::numberCondition(4, 'штук', 'штуки', 'штуka'));

        // 5-20 штук
        $this->assertEquals('штук', Strings::numberCondition(5, 'штук', 'штуки', 'штука'));
        $this->assertEquals('штук', Strings::numberCondition(11, 'штук', 'штуки', 'штука'));
        $this->assertEquals('штук', Strings::numberCondition(20, 'штук', 'штуки', 'штука'));

        // 21-24 штуки (особый случай)
        $this->assertEquals('штука', Strings::numberCondition(21, 'штук', 'штуки', 'штука'));
        $this->assertEquals('штуки', Strings::numberCondition(22, 'штук', 'штуки', 'штука'));
        $this->assertEquals('штуки', Strings::numberCondition(24, 'штук', 'штуки', 'штука'));

        // 25-30 штук
        $this->assertEquals('штук', Strings::numberCondition(25, 'штук', 'штуки', 'штука'));
        $this->assertEquals('штук', Strings::numberCondition(30, 'штук', 'штуки', 'штука'));
    }

    // Test mb_strtr (многобайтовый аналог strtr)
    public function testMbStrtr()
    {
        $this->assertEquals('привет', Strings::mb_strtr('прувет', 'у', 'и'));
        $this->assertEquals('hello', Strings::mb_strtr('hello', '', '')); // empty strings
        $this->assertEquals('测试', Strings::mb_strtr('测试', '试', '试')); // chinese
    }

    // Test mb_str_split (многобайтовый аналог str_split)
    public function testMbStrSplit()
    {
        $this->assertEquals(['п', 'р', 'и', 'в', 'е', 'т'], Strings::mb_str_split('привет'));
        $this->assertEquals(['h', 'e', 'l', 'l', 'o'], Strings::mb_str_split('hello'));
        $this->assertEquals(['测', '试'], Strings::mb_str_split('测试')); // chinese
        $this->assertEquals([], Strings::mb_str_split('')); // empty string
    }

    // Test edge cases for mb_str_split
    public function testMbStrSplitEdgeCases()
    {
        // Строка с пробелами
        $this->assertEquals([' ', 't', 'e', 's', 't', ' '], Strings::mb_str_split(' test '));

        // Строка с спецсимволами
        $this->assertEquals(['©', '®', '™'], Strings::mb_str_split('©®™'));

        // Эмодзи
        $this->assertEquals(['👍', '😊'], Strings::mb_str_split('👍😊'));
    }

    // Test edge cases for numberCondition
    public function testNumberConditionEdgeCases()
    {
        // Большие числа
        $this->assertEquals('штук', Strings::numberCondition(100, 'штук', 'штуки', 'штука'));
        $this->assertEquals('штука', Strings::numberCondition(101, 'штук', 'штуки', 'штука'));
        $this->assertEquals('штуки', Strings::numberCondition(102, 'штук', 'штуки', 'штука'));
        $this->assertEquals('штук', Strings::numberCondition(115, 'штук', 'штуки', 'штуka'));
    }

    // Test empty and special cases for makePath
    public function testMakePathEdgeCases()
    {
        $this->assertEquals('', Strings::makePath([]));
        $this->assertEquals('path'.str_repeat(DIRECTORY_SEPARATOR,2).'to', Strings::makePath(['path', '', 'to'])); // пустой элемент
    }

    // Test that deprecated methods still work correctly
    public function testBackwardCompatibility()
    {
        // Проверяем, что старый CamelCase работает как ожидается
        $this->assertEquals('helloWorld', Strings::CamelCase('hello_world', false));
        $this->assertEquals('TestString', Strings::CamelCase('test_string', true));

        // Проверяем, что работают оба варианта разделителей
        $this->assertEquals('helloWorld', Strings::CamelCase('hello-world', false));
        $this->assertEquals('TestString', Strings::CamelCase('test-string', true));
    }
}
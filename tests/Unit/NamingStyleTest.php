<?php
namespace Unit;

use PHPUnit\Framework\TestCase;
use Sanovskiy\Utility\NamingConvention;
use Sanovskiy\Utility\NamingStyle;

class NamingStyleTest extends TestCase
{
    public function testIsUpperCamelCase()
    {
        $this->assertTrue(NamingStyle::isUpperCamelCase('HelloWorld'));
        $this->assertFalse(NamingStyle::isUpperCamelCase('helloWorld'));
    }

    public function testToSnakeCase()
    {
        $this->assertEquals('hello_world', NamingStyle::toSnakeCase('HelloWorld'));
        $this->assertEquals('hello_world', NamingStyle::toSnakeCase('helloWorld'));
    }

    public function testToCamelCase()
    {
        $this->assertEquals('HelloWorld', NamingStyle::toCamelCase('hello_world', true));
        $this->assertEquals('helloWorld', NamingStyle::toCamelCase('hello_world', false));
    }

    public function testGetNamingStyle()
    {
        $this->assertEquals('UpperCamelCase', NamingStyle::getNamingStyle('HelloWorld'));
        $this->assertEquals('lowerCamelCase', NamingStyle::getNamingStyle('helloWorld'));
        $this->assertEquals('snake_case', NamingStyle::getNamingStyle('hello_world'));
        $this->assertEquals('SCREAMING_SNAKE_CASE', NamingStyle::getNamingStyle('HELLO_WORLD'));
    }

    public function testNamingStyleEdgeCases()
    {
        // Пустая строка
        $this->assertFalse(NamingStyle::isUpperCamelCase(''));
        $this->assertFalse(NamingStyle::isLowerCamelCase(''));
        $this->assertFalse(NamingStyle::isSnakeCase(''));
        $this->assertFalse(NamingStyle::isScreamingSnakeCase(''));
        $this->assertEquals('Unrecognized', NamingStyle::getNamingStyle(''));

        // Строки с пробелами и спецсимволами
        $this->assertFalse(NamingStyle::isUpperCamelCase('Hello World'));
        $this->assertFalse(NamingStyle::isSnakeCase('hello__world')); // Двойное подчёркивание
        $this->assertFalse(NamingStyle::isScreamingSnakeCase('HELLO__WORLD')); // Двойное подчёркивание

        // Некорректные форматы
        $this->assertFalse(NamingStyle::isLowerCamelCase('Hello_World')); // Смешанный стиль
        $this->assertEquals('Unrecognized', NamingStyle::getNamingStyle('hello_World'));
    }

    public function testToSnakeCaseEdgeCases()
    {
        $this->assertEquals('', NamingStyle::toSnakeCase('')); // Пустая строка
        $this->assertEquals('hello_world123', NamingStyle::toSnakeCase('HelloWorld123')); // Цифры
        $this->assertEquals('hello_world123_abc', NamingStyle::toSnakeCase('HelloWorld123Abc'));
        $this->assertEquals('hello123_world', NamingStyle::toSnakeCase('Hello123World'));
        $this->assertEquals('username', NamingStyle::toSnakeCase('username'));
    }

    public function testToCamelCaseEdgeCases()
    {
        $this->assertEquals('', NamingStyle::toCamelCase('', true)); // Пустая строка
        $this->assertEquals('helloWorld123', NamingStyle::toCamelCase('hello_world_123', false)); // Цифры
        $this->assertEquals('username', NamingStyle::toCamelCase('username'));
        $this->assertEquals('Username', NamingStyle::toCamelCase('username', true));
    }

    public function testKebabCase()
    {
        $this->assertTrue(NamingStyle::isKebabCase('hello-world'));
        $this->assertFalse(NamingStyle::isKebabCase('hello_world'));
        $this->assertFalse(NamingStyle::isKebabCase('hello--world'));
        $this->assertEquals('hello-world', NamingStyle::toKebabCase('hello_world'));
        $this->assertEquals('hello-world', NamingStyle::toKebabCase('HelloWorld'));
        $this->assertEquals('HELLO-WORLD', NamingStyle::convert('HELLO_WORLD', NamingConvention::SCREAMING_KEBAB));
    }

    public function testDotCaseAndTrainCase()
    {
        // Detect
        $this->assertTrue(NamingStyle::isDotCase('hello.world'));
        $this->assertTrue(NamingStyle::isTrainCase('Hello-World'));
        $this->assertFalse(NamingStyle::isDotCase('hello_world'));
        $this->assertFalse(NamingStyle::isTrainCase('hello-world')); // kebab, not train

        // Convert
        $this->assertEquals('hello.world', NamingStyle::toDotCase('hello_world'));
        $this->assertEquals('Hello-World', NamingStyle::toTrainCase('hello_world'));
        $this->assertEquals('xml.http.request', NamingStyle::toDotCase('XMLHttpRequest'));
        $this->assertEquals('Xml-Http-Request', NamingStyle::toTrainCase('XMLHttpRequest'));

        // Round-trip
        $this->assertEquals('hello.world', NamingStyle::toDotCase('Hello-World'));
        $this->assertEquals('Hello-World', NamingStyle::toTrainCase('hello.world'));

        // Style name
        $this->assertEquals('dot.case', NamingStyle::getNamingStyle('hello.world'));
        $this->assertEquals('Train-Case', NamingStyle::getNamingStyle('Hello-World'));
    }
}
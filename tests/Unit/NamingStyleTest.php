<?php
namespace Unit;

use PHPUnit\Framework\TestCase;
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
        $this->assertEquals('hello_world_123', NamingStyle::toSnakeCase('HelloWorld123')); // Цифры
        $this->assertEquals('hello_world_123_abc', NamingStyle::toSnakeCase('HelloWorld123Abc'));
        $this->assertEquals('hello_123_world', NamingStyle::toSnakeCase('Hello123World'));
    }

    public function testToCamelCaseEdgeCases()
    {
        $this->assertEquals('', NamingStyle::toCamelCase('', true)); // Пустая строка
        $this->assertEquals('helloWorld123', NamingStyle::toCamelCase('hello_world_123', false)); // Цифры
    }
}
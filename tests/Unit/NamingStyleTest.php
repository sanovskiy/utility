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
}
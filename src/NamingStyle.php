<?php

namespace Sanovskiy\Utility;

class NamingStyle
{
    public static function isUpperCamelCase(string $str): bool
    {
        return preg_match('/^[A-Z][a-zA-Z]*$/', $str) === 1;
    }

    public static function isLowerCamelCase(string $str): bool
    {
        return preg_match('/^[a-z]*[A-Z][a-zA-Z]*$/', $str) === 1;
    }

    public static function isSnakeCase(string $str): bool
    {
        return preg_match('/^[a-z]+(_[a-z]+)*$/', $str) === 1;
    }

    public static function isScreamingSnakeCase(string $str): bool
    {
        return preg_match('/^[A-Z]+(_[A-Z]+)*$/', $str) === 1;
    }

    public static function toSnakeCase(string $str): string
    {
        if (!self::isUpperCamelCase($str) && !self::isLowerCamelCase($str)){
            return $str;
        }
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $str));
    }

    public static function toCamelCase(string $str, bool $capitalizeFirstCharacter = false): string
    {
        if (!self::isSnakeCase($str) && !self::isScreamingSnakeCase($str)){
            return $str;
        }
        if (self::isScreamingSnakeCase($str)){
            $str = strtolower($str);
        }
        $str = str_replace('_', ' ', $str);
        $str = ucwords($str);
        $str = str_replace(' ', '', $str);

        if (!$capitalizeFirstCharacter) {
            $str = lcfirst($str);
        }

        return $str;
    }

    public static function swapStyle(string $string): string
    {
        if (self::isLowerCamelCase($string) || self::isUpperCamelCase($string)){
            return self::toSnakeCase($string);
        }
        if (self::isSnakeCase($string) || self::isScreamingSnakeCase($string)){
            return self::toCamelCase($string, true);
        }
        return $string;
    }

    public static function getNamingStyle(string $str): string
    {
        return match (true) {
            self::isUpperCamelCase($str) => 'UpperCamelCase',
            self::isLowerCamelCase($str) => 'lowerCamelCase',
            self::isSnakeCase($str) => "snake_case",
            self::isScreamingSnakeCase($str) => "SCREAMING_SNAKE_CASE",
            default => "Unrecognized",
        };
    }
}

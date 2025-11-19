<?php

namespace Sanovskiy\Utility;

class NamingStyle
{
    // === СТАРЫЙ ПУБЛИЧНЫЙ API — НЕ ТРОГАЕМ ===

    public static function isUpperCamelCase(string $str): bool
    {
        return self::detect($str) === NamingConvention::UPPER_CAMEL;
    }

    public static function isLowerCamelCase(string $str): bool
    {
        return self::detect($str) === NamingConvention::LOWER_CAMEL;
    }

    public static function isSnakeCase(string $str): bool
    {
        return self::detect($str) === NamingConvention::SNAKE;
    }

    public static function isScreamingSnakeCase(string $str): bool
    {
        return self::detect($str) === NamingConvention::SCREAMING_SNAKE;
    }

    public static function toSnakeCase(string $str): string
    {
        return self::convert($str, NamingConvention::SNAKE);
    }

    public static function toCamelCase(string $str, bool $capitalizeFirstCharacter = false): string
    {
        $target = $capitalizeFirstCharacter
            ? NamingConvention::UPPER_CAMEL
            : NamingConvention::LOWER_CAMEL;
        return self::convert($str, $target);
    }

    public static function isKebabCase(string $str): bool
    {
        return self::detect($str) === NamingConvention::KEBAB;
    }

    public static function toKebabCase(string $str): string
    {
        return self::convert($str, NamingConvention::KEBAB);
    }

    public static function isDotCase(string $str): bool
    {
        return self::detect($str) === NamingConvention::DOT;
    }

    public static function isTrainCase(string $str): bool
    {
        return self::detect($str) === NamingConvention::TRAIN;
    }

    public static function toDotCase(string $str): string
    {
        return self::convert($str, NamingConvention::DOT);
    }

    public static function toTrainCase(string $str): string
    {
        return self::convert($str, NamingConvention::TRAIN);
    }

    /**
     * @param string $string
     * @return string
     * @deprecated This method is outdated and will be removed in future versions
     */
    public static function swapStyle(string $string): string
    {
        return match (self::detect($string)) {
            NamingConvention::UPPER_CAMEL, NamingConvention::LOWER_CAMEL =>
            self::convert($string, NamingConvention::SNAKE),
            NamingConvention::SNAKE, NamingConvention::SCREAMING_SNAKE, NamingConvention::KEBAB, NamingConvention::SCREAMING_KEBAB =>
            self::convert($string, NamingConvention::UPPER_CAMEL),
            default => $string,
        };
    }

    public static function getNamingStyle(string $str): string
    {
        return match (self::detect($str)) {
            NamingConvention::UPPER_CAMEL => 'UpperCamelCase',
            NamingConvention::LOWER_CAMEL => 'lowerCamelCase',
            NamingConvention::SNAKE => 'snake_case',
            NamingConvention::SCREAMING_SNAKE => 'SCREAMING_SNAKE_CASE',
            NamingConvention::KEBAB => 'kebab-case',
            NamingConvention::SCREAMING_KEBAB => 'SCREAMING-KEBAB-CASE',
            NamingConvention::DOT => 'dot.case',
            NamingConvention::TRAIN => 'Train-Case',
            default => 'Unrecognized',
        };
    }

    // === НОВЫЙ ВНУТРЕННИЙ API ===

    public static function detect(string $str): ?NamingConvention
    {
        if (preg_match('/^[A-Z][a-zA-Z\d]*$/', $str) === 1) {
            return NamingConvention::UPPER_CAMEL;
        }
        if (preg_match('/^[a-z]+[A-Z][a-zA-Z\d]*$/', $str) === 1) {
            return NamingConvention::LOWER_CAMEL;
        }
        if (preg_match('/^[a-z\d]+(_[a-z\d]+)*$/', $str) === 1) {
            return NamingConvention::SNAKE;
        }
        if (preg_match('/^[A-Z\d]+(_[A-Z\d]+)*$/', $str) === 1) {
            return NamingConvention::SCREAMING_SNAKE;
        }
        if (preg_match('/^[a-z\d]+(-[a-z\d]+)*$/', $str) === 1) {
            return NamingConvention::KEBAB;
        }
        if (preg_match('/^[A-Z\d]+(-[A-Z\d]+)*$/', $str) === 1) {
            return NamingConvention::SCREAMING_KEBAB;
        }
        if (preg_match('/^[a-z\d]+(\.[a-z\d]+)*$/', $str) === 1) {
            return NamingConvention::DOT;
        }
        if (preg_match('/^[A-Z][a-z\d]*(-[A-Z][a-z\d]*)*$/', $str) === 1) {
            return NamingConvention::TRAIN;
        }
        return null;
    }

    public static function convert(string $input, NamingConvention $target): string
    {
        $current = self::detect($input);
        if ($current === $target) {
            return $input;
        }
        if ($current === null) {
            return $input; // не распознан — не трогаем
        }

        // Промежуточное представление: нормализуем в слова
        $words = self::splitToWords($input, $current);

        // Собираем в целевой стиль
        return self::joinWords($words, $target);
    }

    private static function splitToWords(string $str, NamingConvention $style): array
    {
        return match ($style) {
            NamingConvention::UPPER_CAMEL, NamingConvention::LOWER_CAMEL =>
            self::splitCamelCaseToWords($str),
            NamingConvention::SNAKE, NamingConvention::SCREAMING_SNAKE =>
            explode('_', $str),
            NamingConvention::TRAIN, NamingConvention::KEBAB, NamingConvention::SCREAMING_KEBAB =>
            explode('-', $str),
            NamingConvention::DOT => explode('.', $str),
        };
    }

    private static function splitCamelCaseToWords(string $str): array
    {
        $s1 = preg_replace('/(.)([A-Z][a-z]+)/', '$1 $2', $str);
        $s2 = preg_replace('/([a-z\d])([A-Z])/', '$1 $2', $s1);
        return array_filter(explode(' ', $s2));
    }

    private static function joinWords(array $words, NamingConvention $style): string
    {
        return match ($style) {
            NamingConvention::UPPER_CAMEL =>
            implode('', array_map('ucfirst', $words)),
            NamingConvention::LOWER_CAMEL =>
            lcfirst(implode('', array_map('ucfirst', $words))),
            NamingConvention::SNAKE =>
            strtolower(implode('_', $words)),
            NamingConvention::SCREAMING_SNAKE =>
            strtoupper(implode('_', $words)),
            NamingConvention::KEBAB =>
            strtolower(implode('-', $words)),
            NamingConvention::SCREAMING_KEBAB =>
            strtoupper(implode('-', $words)),
            NamingConvention::TRAIN =>
            implode('-', array_map('ucfirst', array_map('strtolower', $words))),
            NamingConvention::DOT =>
            strtolower(implode('.', $words)),
        };
    }
}

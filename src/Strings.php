<?php

namespace Sanovskiy\Utility;

use JetBrains\PhpStorm\Pure;

/**
 * Class String
 * @package App\Components\Utils;
 */
class Strings
{
    /**
     * @param string $string
     * @return bool
     */
    #[Pure] public static function isURL(string $string): bool
    {
        return (boolean)filter_var($string, FILTER_VALIDATE_URL);
    }

    /**
     * @param string[] $pathParts
     * @return string
     */
    #[Pure] public static function makePath(array $pathParts): string
    {
        return implode(DIRECTORY_SEPARATOR, $pathParts);
    }

    /**
     * @param string $string
     * @param bool $firstLetterCaps
     * @return string
     */
    public static function CamelCase(string $string, bool $firstLetterCaps = true): string
    {
        $arr = array_map('ucfirst', explode('_', preg_replace('/[_-]/', '_', $string)));
        if (!$firstLetterCaps) {
            $arr[0] = strtolower($arr[0]);
        }
        return implode('', $arr);
    }

    /**
     * @param int $num - number
     * @param string $gen - for (шту)к
     * @param string $plu - for (шту)ки
     * @param string $sin - for (шту)ка
     * @return string
     */
    #[Pure] public static function numberCondition(int $num, string $gen, string $plu, string $sin): string
    {
        if (self::strEndsWith((string)$num, '1') && (strlen((string)$num) < 2 || substr((string)$num, -2, 1) !== '1')) {
            return $sin;
        }

        if (self::strEndsWith((string)$num, ['2', '3', '4']) && (strlen((string)$num) < 2 || substr(
                    (string)$num,
                    -2,
                    1
                ) !== '1')) {
            return $plu;
        }
        return $gen;
    }

    /**
     * @param string $haystack
     * @param string|array $needles
     * @return bool
     */
    public static function strEndsWith(string $haystack, string|array $needles): bool
    {
        if (!is_array($needles)) {
            $needles = [$needles];
        }
        foreach ($needles as $needle) {
            if (str_ends_with($haystack, $needle)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $str
     * @param string $from
     * @param string $to
     * @return string|string[]
     */
    public static function mb_strtr(string $str, string $from, string $to): array|string
    {
        return str_replace(self::mb_str_split($from), self::mb_str_split($to), $str);
    }

    /**
     * @param string $str
     * @return array|bool
     */
    #[Pure] public static function mb_str_split(string $str): array|bool
    {
        return preg_split('//u', $str, null, PREG_SPLIT_NO_EMPTY);
    }
}
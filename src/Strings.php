<?php namespace Sanovskiy\Utility;

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
     * @deprecated Use NamingStyle::toCamelCase() instead
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
     * @param string $genitive - for (шту)к
     * @param string $plural - for (шту)ки
     * @param string $singular - for (шту)ка
     * @return string
     */
    public static function numberCondition(int $num, string $genitive, string $plural, string $singular): string
    {
        if (($num % 100) > 10 && ($num % 100) < 20) {
            return $genitive;
        }

        return match ($num % 10) {
            1 => $singular,
            2, 3, 4 => $plural,
            default => $genitive,
        };
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

    /**
     * @param ...$strings
     * @return array
     */
    public static function removeCommonPrefix(...$strings): array
    {
        $commonPrefix = array_reduce($strings, function ($prefix, $str) {
            $length = min(strlen($prefix), strlen($str));
            for ($i = 0; $i < $length; $i++) {
                if ($prefix[$i] !== $str[$i]) {
                    return substr($prefix, 0, $i);
                }
            }
            return substr($prefix, 0, $length);
        }, $strings[0]);

        return array_map(function ($str) use ($commonPrefix) {
            return substr($str, strlen($commonPrefix));
        }, $strings);
    }
}
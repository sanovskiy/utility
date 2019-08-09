<?php namespace Sanovskiy\Utility;

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
    public static function isURL(string $string): bool
    {
        return (boolean)filter_var($string, FILTER_VALIDATE_URL);
    }

    /**
     * @param string[] $pathParts
     *
     * @return string
     */
    public static function makePath(array $pathParts): string
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
        $string = preg_replace('/[_-]/', '_', $string);
        $arr = explode('_', $string);
        $arr = array_map('ucfirst', $arr);
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
    public static function numberCondition(int $num, string $gen, string $plu, string $sin)
    {
        if (substr((string)$num, -1, 1) === '1' &&
            (
                strlen((string)$num) < 2 ||
                substr((string)$num, -2, 1) !== '1'
            )
        ) {
            return $sin;
        }
        if (
            in_array(substr((string)$num, -1, 1), ['2', '3', '4'], true) &&
            (
                strlen((string)$num) < 2 ||
                substr((string)$num, -2, 1) !== '1'
            )
        ) {
            return $plu;
        }
        return $gen;
    }

    /**
     * @param string $str
     * @param string $from
     * @param string $to
     * @return string|array
     */
    public static function mb_strtr(string $str, string $from, string $to)
    {
        return str_replace(self::mb_str_split($from), self::mb_str_split($to), $str);
    }

    /**
     * @param string $str
     * @return array[]|false|string[]
     */
    public static function mb_str_split(string $str)
    {
        return preg_split('//u', $str, null, PREG_SPLIT_NO_EMPTY);
    }
}
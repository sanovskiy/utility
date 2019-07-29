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

}
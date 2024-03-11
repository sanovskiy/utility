<?php

namespace Sanovskiy\Utility;

class Arrays
{
    public static function smartMerge(array $array1, array $array2): array
    {
        $arrayResult = $array1;
        foreach ($array2 as $key => $value) {
            if (!isset($arrayResult[$key])) {
                $arrayResult[$key] = $value;
                continue;
            }
            if (is_array($value) && is_array($arrayResult[$key])) {
                $arrayResult[$key] = self::smartMerge($arrayResult[$key], $value);
                continue;
            }
            $arrayResult[$key] = $value;
        }
        return $arrayResult;
    }

}
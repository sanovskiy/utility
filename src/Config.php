<?php
namespace Sanovskiy\Utility;

use RuntimeException;

class Config extends Repository
{
    protected function init()
    {
        foreach ($this->records as $key => $item) {
            if (is_array($item)) {
                $this->records[$key] = new static($item);
            }
        }
    }


    /**
     * @param string $pathToConfigs
     * @param string $env
     * @return array
     */
    public static function loadConfigArray(string $pathToConfigs, string $env): array
    {
        if (!file_exists($pathToConfigs) || !is_dir($pathToConfigs)) {
            throw new RuntimeException(sprintf("%s is not a real directory", $pathToConfigs));
        }
        $filename = sprintf("%s%s%s.php", realpath($pathToConfigs), DIRECTORY_SEPARATOR, $env);
        if (!file_exists($filename) || !is_readable($filename)) {
            throw new RuntimeException(sprintf("No config found for %s", $env));
        }
        $config = include $filename;
        if (isset($config['parent_env'])) {
            $config = Arrays::smartMerge(self::loadConfigArray($pathToConfigs, $config['parent_env']), $config);
            unset($config['parent_env']);
        }
        return $config;
    }

    /**
     * @param mixed $arrayOriginal
     * @param mixed $arrayToMerge
     * @return array
     * @deprecated Use Arrays::smartMerge()
     */
    public static function mergeArray(mixed $arrayOriginal, mixed $arrayToMerge): array
    {
        return Arrays::smartMerge($arrayOriginal,$arrayToMerge);
    }
}
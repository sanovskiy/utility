<?php namespace Sanovskiy\Utility;

use RuntimeException;

class Config extends Repository
{
    /**
     * Core_Config constructor.
     * @param $config
     */
    public function __construct($config)
    {
        foreach ($config as $key => $item) {
            if (is_array($item)) {
                $this->records[$key] = new self($item);
                continue;
            }
            $this->records[$key] = $item;
        }
    }

    /**
     * @param string $pathToConfigs
     * @param string $env
     * @return mixed
     */
    public static function loadConfigArray(string $pathToConfigs, string $env): mixed
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
            $config = self::mergeArray(self::loadConfigArray($pathToConfigs, $config['parent_env']), $config);
            unset($config['parent_env']);
        }
        return $config;
    }

    /**
     * @param mixed $arrayOriginal
     * @param mixed $arrayToMerge
     * @return array
     */
    public static function mergeArray(mixed $arrayOriginal, mixed $arrayToMerge): array
    {
        $arrayResult = $arrayOriginal;
        foreach ($arrayToMerge as $key => $value) {
            if (!array_key_exists($key, $arrayResult)) {
                $arrayResult[$key] = $value;
                continue;
            }
            if (is_array($value) && is_array($arrayResult[$key])) {
                $arrayResult[$key] = self::mergeArray($arrayResult[$key], $value);
                continue;
            }
            $arrayResult[$key] = $value;
        }
        return $arrayResult;
    }
}
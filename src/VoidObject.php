<?php

namespace Sanovskiy\Utility;

class VoidObject
{
    /**
     * @param $name
     * @param $arguments
     * @return bool
     */
    public static function __callStatic($name, $arguments)
    {
        return true;
    }

    /**
     * @return self
     */
    public function __invoke()
    {
        return $this;
    }

    /**
     * @param $name
     */
    public function __unset($name)
    {
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '';
    }

    /**
     * @param $name
     * @param $arguments
     * @return $this
     */
    public function __call($name, $arguments)
    {
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function __get($name)
    {
        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @return mixed
     */
    public function __set($name, $value)
    {
        return $value;
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return true;
    }
}
<?php namespace Sanovskiy\Utility;

use Sanovskiy\Traits\ArrayAccess;
use Sanovskiy\Traits\Countable;
use Sanovskiy\Traits\Iterator;

/**
 * Class Repository
 * @package App\Components\Core;
 */
class Repository implements \ArrayAccess, \Iterator, \Countable
{
    use Iterator;
    use Countable;
    use ArrayAccess;

    protected $records = [];

    /**
     * @param array $arr
     * @return Repository
     */
    public static function fromArray(array $arr)
    {
        $obj = new self;
        foreach ($arr as $key => $val) {
            $obj->{$key} = $val;
        }
        return $obj;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function keyExists(string $key)
    {
        return array_key_exists($key, $this->records);
    }

    /**
     * @param $name
     * @return mixed
     */
    function __get($name)
    {
        if (!isset($this->records[$name])) {
            return null;
        }
        return $this->records[$name];
    }

    /**
     * @param $name
     * @param $value
     * @return mixed
     */
    function __set($name, $value)
    {
        return $this->records[$name] = $value;
    }

    /**
     * @param $name
     * @return bool
     */
    function __isset($name): bool
    {
        return array_key_exists($name, $this->records);
    }

    /**
     * @return array
     */
    function __debugInfo()
    {
        return $this->records;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->records;
    }

    /**
     * @return string
     */
    public function getLayout(): string
    {
        return $this->layout;
    }

    /**
     * @param string $layout
     */
    public function setLayout(string $layout)
    {
        $this->layout = $layout;
    }
}
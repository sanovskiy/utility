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
        $obj = new static;
        foreach ($arr as $key => $val) {
            $obj->{$key} = $val;
        }
        return $obj;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function keyExists($key)
    {
        return array_key_exists($key, $this->records);
    }

    /**
     * @param string|int $name
     * @return mixed
     */
    function __get( $name)
    {
        if (!isset($this->records[$name])) {
            return null;
        }
        return $this->records[$name];
    }

    /**
     * @param string|int $name
     * @param mixed $value
     * @return void
     */
    function __set($name, $value)
    {
        $this->records[$name] = $value;
    }

    /**
     * @param string|int $name
     * @return bool
     */
    function __isset($name)
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
        $result = [];
        foreach ($this->records as $key => $val) {
            if ($val instanceof Repository) {
                $val = $val->toArray();
            }
            $result[$key] = $val;

        }

        return $result;
    }

}
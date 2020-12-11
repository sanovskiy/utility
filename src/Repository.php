<?php namespace Sanovskiy\Utility;

use JetBrains\PhpStorm\Pure;
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

    protected array $records = [];

    /**
     * @var string
     * @deprecated
     */
    protected string $layout;

    /**
     * @param array $arr
     * @return Repository
     */
    public static function fromArray(array $arr): Repository
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
    #[Pure] public function keyExists(string $key): bool
    {
        return array_key_exists($key, $this->records);
    }

    /**
     * @param string|int $name
     * @return mixed
     */
    function __get(string|int $name): mixed
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
    function __set(int|string $name, mixed $value)
    {
        $this->records[$name] = $value;
    }

    /**
     * @param string|int $name
     * @return bool
     */
    #[Pure] function __isset(string|int $name): bool
    {
        return array_key_exists($name, $this->records);
    }

    /**
     * @return array
     */
    function __debugInfo(): array
    {
        return $this->records;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->records as $key => $val) {
            if ($val instanceof self) {
                $val = $val->toArray();
            }
            $result[$key] = $val;

        }

        return $result;
    }

    /**
     * @return string
     * @deprecated
     */
    public function getLayout(): string
    {
        return $this->layout;
    }

    /**
     * @param string $layout
     * @deprecated
     */
    public function setLayout(string $layout)
    {
        $this->layout = $layout;
    }
}
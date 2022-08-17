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
     * @param array $data
     * @return static
     * @deprecated Use new Repository($data)
     */
    public static function fromArray(array $data): static
    {
        return new static($data);
    }

    public function __construct(array $data)
    {
        foreach ($data as $key => $val) {
            $this->records['$key'] = $val;
        }
        $this->init();
    }


    protected function init(){}

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
    public function __get(string|int $name): mixed
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
    public function __set(int|string $name, mixed $value)
    {
        $this->records[$name] = $value;
    }

    /**
     * @param string|int $name
     * @return bool
     */
    #[Pure] public function __isset(string|int $name): bool
    {
        return array_key_exists($name, $this->records);
    }

    /**
     * @return array
     */
    public function __debugInfo(): array
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
}
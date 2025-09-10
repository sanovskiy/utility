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
            $this->records[$key] = $val;
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
     * Check if an item exists in the repository using dot notation.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        $array = $this->records;

        if (array_key_exists($key, $array)) {
            return true;
        }

        if (!str_contains($key, '.')) {
            return false;
        }

        foreach (explode('.', $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Get an item from the repository using dot notation.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $array = $this->records;

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        if (!str_contains($key, '.')) {
            return $default;
        }

        foreach (explode('.', $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }

    /**
     * Set an item in the repository using dot notation.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        $array = &$this->records;
        $keys = explode('.', $key);
        $lastKey = array_pop($keys);

        foreach ($keys as $k) {
            if (!isset($array[$k]) || !is_array($array[$k])) {
                $array[$k] = [];
            }
            $array = &$array[$k];
        }

        $array[$lastKey] = $value;
    }

    /**
     * @param string|int $name
     * @return mixed
     */
    public function __get(string|int $name): mixed
    {
        if (str_contains($name, '.')) {
            return $this->get($name);
        }

        return $this->records[$name] ?? null;
    }

    /**
     * @param string|int $name
     * @param mixed $value
     * @return void
     */
    public function __set(int|string $name, mixed $value)
    {
        if (str_contains($name, '.')) {
            $this->set($name, $value);
            return;
        }

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
     * Get all keys from the repository.
     *
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this->records);
    }

    /**
     * @return array
     */
    public function __debugInfo(): array
    {
        return $this->records;
    }

    /**
     * Magic method for method chaining with configuration.
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        if (str_starts_with($name, 'get')) {
            $key = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', substr($name, 3)));
            return $this->get($key, $arguments[0] ?? null);
        }

        if (str_starts_with($name, 'set') && count($arguments) === 1) {
            $key = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', substr($name, 3)));
            $this->set($key, $arguments[0]);
            return $this;
        }

        if (str_starts_with($name, 'has')) {
            $key = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', substr($name, 3)));
            return $this->has($key);
        }

        throw new \BadMethodCallException("Method $name does not exist");
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->records as $key => $val) {
            if ($val instanceof static) {
                $val = $val->toArray();
            }
            $result[$key] = $val;
        }
        return $result;
    }
}
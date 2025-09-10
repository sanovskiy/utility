<?php

declare(strict_types=1);

namespace Sanovskiy\Utility;

use JetBrains\PhpStorm\Pure;

/**
 * Class Repository
 * @package Sanovskiy\Utility
 */
class Repository implements \ArrayAccess, \Iterator, \Countable
{
    use \Sanovskiy\Traits\Iterator;
    use \Sanovskiy\Traits\Countable;

    protected array $records = [];

    /**
     * Constructor initializes the repository with an array of data.
     *
     * @param array $data Initial data
     */
    public function __construct(array $data = [])
    {
        $this->records = $data;
        $this->init();
    }

    /**
     * Initialize the repository (hook for subclasses).
     */
    protected function init(): void
    {
    }

    /**
     * Wrap a value in a Repository instance if it’s an array.
     *
     * @param mixed $value Value to process
     * @return mixed Processed value
     */
    protected function wrapValue(mixed $value): mixed
    {
        if (is_array($value)) {
            return new self($value);
        }
        return $value;
    }

    /**
     * Check if a key exists using dot notation.
     *
     * @param string|int $key Key to check
     * @return bool
     */
    #[Pure]
    public function has(string|int $key): bool
    {
        if ($key === '') {
            return array_key_exists('', $this->records);
        }

        // Try dot notation if the key is a string and contains a dot
        if (is_string($key) && str_contains($key, '.')) {
            $keys = explode('.', $key);
            $current = $this->records;

            foreach ($keys as $index => $k) {
                if ($current instanceof self) {
                    return $current->has(implode('.', array_slice($keys, $index)));
                }
                if (!is_array($current) || !array_key_exists($k, $current)) {
                    return false;
                }
                $current = $current[$k];
            }

            return true;
        }

        // Fallback to literal key (int or string)
        return array_key_exists($key, $this->records);
    }

    /**
     * Get a value using dot notation.
     *
     * @param string|int $key Key to retrieve
     * @param mixed $default Default value if key not found
     * @param bool $useLiteralKey Whether to treat the key as literal (no dot notation)
     * @return mixed
     */
    public function get(string|int $key, mixed $default = null, bool $useLiteralKey = false): mixed
    {
        if ($key === '') {
            return $this->has('') ? $this->records[''] : $default;
        }

        // Use literal key if explicitly requested
        if ($useLiteralKey && $this->has($key)) {
            return $this->records[$key];
        }

        // Try dot notation if the key is a string and contains a dot
        if (is_string($key) && str_contains($key, '.')) {
            $keys = explode('.', $key);
            $current = $this->records;

            foreach ($keys as $index => $k) {
                if ($current instanceof self) {
                    return $current->get(implode('.', array_slice($keys, $index)), $default);
                }
                if (!is_array($current) || !array_key_exists($k, $current)) {
                    return $default;
                }
                $current = $current[$k];
            }

            return $current;
        }

        // Fallback to literal key (int or string)
        return $this->has($key) ? $this->records[$key] : $default;
    }

    /**
     * Set a value using dot notation.
     *
     * @param string|int $key Key to set
     * @param mixed $value Value to set
     * @return void
     */
    public function set(string|int $key, mixed $value): void
    {
        if ($key === '') {
            $this->records[''] = $value;
            return;
        }

        // Use literal key if not a string or no dots
        if (!is_string($key) || !str_contains($key, '.')) {
            $this->records[$key] = $value;
            return;
        }

        $keys = explode('.', $key);
        $lastKey = array_pop($keys);
        $current = &$this->records;

        foreach ($keys as $index => $k) {
            if (!isset($current[$k])) {
                $current[$k] = new static([]);
            } elseif ($current[$k] instanceof self) {
                $remainingKeys = array_slice($keys, $index + 1);
                $remainingKeys[] = $lastKey;
                $current[$k]->set(implode('.', $remainingKeys), $value);
                return;
            }
            if (!isset($current[$k]) || !is_array($current[$k])) {
                $current[$k] = [];
            }
            $current = &$current[$k];
        }

        $current[$lastKey] = $value;
    }

    /**
     * Magic getter for property access.
     *
     * @param string|int $name Property name
     * @return mixed
     */
    public function __get(string|int $name): mixed
    {
        if ($this->has($name)) {
            return $this->wrapValue($this->records[$name]);
        }
        return $this->get($name);
    }

    /**
     * ArrayAccess: Get a value by offset.
     *
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
        if ($this->has($offset)) {
            return $this->wrapValue($this->records[$offset]);
        }
        return $this->get($offset);
    }

    /**
     * ArrayAccess: Set a value by offset.
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * ArrayAccess: Check if an offset exists.
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }

    /**
     * ArrayAccess: Unset an offset.
     *
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        if ($offset === '' || (is_string($offset) && empty($offset))) {
            throw new \InvalidArgumentException('Key must be a non-empty string');
        }
        unset($this->records[$offset]);
    }

    /**
     * Magic setter for property access.
     *
     * @param string|int $name Property name
     * @param mixed $value Value to set
     * @return void
     */
    public function __set(string|int $name, mixed $value): void
    {
        if ($name === '' || (is_string($name) && empty($name))) {
            throw new \InvalidArgumentException('Key must be a non-empty string');
        }
        $this->set($name, $value);
    }

    /**
     * Check if a property is set.
     *
     * @param string|int $name Property name
     * @return bool
     */
    #[Pure]
    public function __isset(string|int $name): bool
    {
        return $this->has($name);
    }

    /**
     * Get all keys in the repository.
     *
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this->records);
    }

    /**
     * Convert the repository to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->records as $key => $value) {
            $result[$key] = $value instanceof self ? $value->toArray() : $value;
        }
        return $result;
    }

    /**
     * Provide debug information.
     *
     * @return array
     */
    public function __debugInfo(): array
    {
        return $this->toArray();
    }
}
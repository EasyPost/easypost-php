<?php

namespace EasyPost;

use EasyPost\Constant\Constants;
use EasyPost\Util\InternalUtil;
use EasyPost\Util\Util;

// @phpstan-ignore-next-line
class EasyPostObject implements \ArrayAccess, \Iterator
{
    /**
     * @var array<mixed>
     */
    protected array $_values;

    /**
     * @var array<mixed>
     */
    protected array $_immutableValues;

    private mixed $_parent;
    private mixed $_name;

    /**
     * Constructor for EasyPost objects.
     *
     * @param mixed $parent
     * @param mixed $name
     */
    public function __construct(mixed $parent = null, mixed $name = null)
    {
        $this->_values = [];
        $this->_immutableValues = ['id'];
        $this->_parent = $parent;
        $this->_name = $name;
    }

    /**
     * Standard accessor magic methods.
     *
     * @param string $k
     * @param mixed $v
     */
    public function __set(string $k, mixed $v): void
    {
        $this->_values[$k] = $v;

        $i = 0;
        $current = $this;
        $param = [$k => $v];

        // TODO: Rework this when we fix (de)serialization
        // @phpstan-ignore-next-line
        while (true && $i < 99) {
            if (!is_null($current->_parent)) {
                $param = [$current->_name => $param];
                $current = $current->_parent;
            }
            $i++;
        }
    }

    /**
     * `isset` magic method.
     *
     * @param string $k
     * @return bool
     */
    public function __isset(string $k): bool
    {
        return isset($this->_values[$k]);
    }

    /**
     * `unset` magic method.
     *
     * @param string $k
     */
    public function __unset(string $k): void
    {
        if (!in_array($k, $this->_immutableValues)) {
            unset($this->_values[$k]);

            $i = 0;
            $current = $this;
            $param = [$k => null];

            // TODO: Rework this when we fix (de)serialization
            // @phpstan-ignore-next-line
            while (true && $i < 99) {
                if (!is_null($current->_parent)) {
                    $param = [$current->_name => $param];
                    $current = $current->_parent;
                }
                $i++;
            }
        }
    }

    /**
     * Getter.
     *
     * @param string $k
     * @return mixed
     */
    public function __get(string $k): mixed
    {
        if (array_key_exists($k, $this->_values)) {
            return $this->_values[$k];
        } else {
            $class = get_class($this);
            error_log(sprintf(Constants::UNDEFINED_PROPERTY_ERROR, $class, $k));

            return null;
        }
    }

    /**
     * Construct EasyPost objects from a response.
     *
     * @param EasyPostClient|null $client
     * @param array<mixed> $values
     * @param string $class
     * @return mixed
     */
    public static function constructFrom(?EasyPostClient $client, array $values, string $class): mixed
    {
        /** @var EasyPostObject $easypostObject */
        $easypostObject = new $class($client);
        $easypostObject->convertEach($client, $values);

        return $easypostObject;
    }

    /**
     * Convert each piece of an EasyPost object.
     *
     * @param EasyPostClient|null $client
     * @param array<mixed> $values
     */
    public function convertEach(?EasyPostClient $client, array $values): void
    {
        foreach ($values as $k => $v) {
            // We don't want `_params` to become the default `EasyPostObject` since it needs to remain a normal array
            if ($k == '_params') {
                $this->_values[$k] = $v;
            } else {
                $this->_values[$k] = InternalUtil::convertToEasyPostObject($client, $v);
            }
        }
    }

    /**
     * ArrayAccess methods.
     *
     * @param mixed $k
     * @param mixed $v
     */
    public function offsetSet(mixed $k, mixed $v): void
    {
        $this->$k = $v;
    }

    /**
     * ArrayAccess methods.
     *
     * @param mixed $k
     * @return bool
     */
    public function offsetExists(mixed $k): bool
    {
        return array_key_exists($k, $this->_values);
    }

    /**
     * ArrayAccess methods.
     *
     * @param mixed $k
     */
    public function offsetUnset(mixed $k): void
    {
        unset($this->$k);
    }

    /**
     * ArrayAccess methods.
     *
     * @param mixed $k
     * @return mixed
     */
    public function offsetGet(mixed $k): mixed
    {
        return array_key_exists($k, $this->_values) ? $this->_values[$k] : null;
    }

    /**
     * Iterator methods.
     *
     * @return void
     */
    public function rewind(): void
    {
        reset($this->_values);
    }

    /**
     * Iterator methods.
     *
     * @return mixed
     */
    public function current(): mixed
    {
        return current($this->_values);
    }

    /**
     * Iterator methods.
     *
     * @return mixed
     */
    public function key(): mixed
    {
        return key($this->_values);
    }

    /**
     * Iterator methods.
     *
     * @return void
     */
    public function next(): void
    {
        next($this->_values);
    }

    /**
     * Iterator methods.
     *
     * @return bool
     */
    public function valid(): bool
    {
        $key = key($this->_values);
        return $key !== null;
    }

    /**
     * Convert object to JSON.
     *
     * @return string|bool
     */
    public function __toJSON(): string|bool
    {
        if (defined('JSON_PRETTY_PRINT')) {
            return json_encode($this->__toArray(true), JSON_PRETTY_PRINT);
        }

        return json_encode($this->__toArray(true));
    }

    /**
     * Convert object to a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->__toJSON();
    }

    /**
     * Convert object to an array.
     *
     * @param bool|null $recursive
     * @return array<mixed>
     */
    public function __toArray(?bool $recursive = false): array
    {
        if ($recursive) {
            return Util::convertEasyPostObjectToArray($this->_values);
        }

        return $this->_values;
    }
}

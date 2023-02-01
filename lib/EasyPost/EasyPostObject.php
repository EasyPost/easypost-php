<?php

namespace EasyPost;

use EasyPost\Constant\Constants;
use EasyPost\Util\InternalUtil;
use EasyPost\Util\Util;

class EasyPostObject implements \ArrayAccess, \Iterator
{
    /**
     * @var array
     */
    protected $_values;

    /**
     * @var array
     */
    protected $_immutableValues;

    /**
     * @var string
     */
    private $_parent;

    /**
     * @var string
     */
    private $_name;

    /**
     * Constructor for EasyPost objects.
     *
     * @param string $parent
     * @param string $name
     */
    public function __construct($parent = null, $name = null)
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
    public function __set($k, $v)
    {
        $this->_values[$k] = $v;

        $i = 0;
        $current = $this;
        $param = [$k => $v];

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
    public function __isset($k)
    {
        return isset($this->_values[$k]);
    }

    /**
     * `unset` magic method.
     *
     * @param string $k
     */
    public function __unset($k)
    {
        if (!in_array($k, $this->_immutableValues)) {
            unset($this->_values[$k]);

            $i = 0;
            $current = $this;
            $param = [$k => null];

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
    public function __get($k)
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
     * @param EasyPostClient $client
     * @param array $values
     * @param string $class
     * @return mixed
     */
    public static function constructFrom($client, $values, $class)
    {
        $object = new $class($client);
        $object->convertEach($client, $values);

        return $object;
    }

    /**
     * Convert each piece of an EasyPost object.
     *
     * @param EasyPostClient $client
     * @param array $values
     */
    public function convertEach($client, $values)
    {
        foreach ($values as $k => $v) {
            $this->_values[$k] = InternalUtil::convertToEasyPostObject($client, $v);
        }
    }

    /**
     * ArrayAccess methods.
     *
     * @param string $k
     * @param mixed $v
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($k, $v)
    {
        $this->$k = $v;
    }

    /**
     * ArrayAccess methods.
     *
     * @param string $k
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($k)
    {
        return array_key_exists($k, $this->_values);
    }

    /**
     * ArrayAccess methods.
     *
     * @param string $k
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($k)
    {
        unset($this->$k);
    }

    /**
     * ArrayAccess methods.
     *
     * @param string $k
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($k)
    {
        return array_key_exists($k, $this->_values) ? $this->_values[$k] : null;
    }

    /**
     * Iterator methods.
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function rewind()
    {
        reset($this->_values);
    }

    /**
     * Iterator methods.
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return current($this->_values);
    }

    /**
     * Iterator methods.
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return key($this->_values);
    }

    /**
     * Iterator methods.
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function next()
    {
        return next($this->_values);
    }

    /**
     * Iterator methods.
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function valid()
    {
        $key = key($this->_values);
        return ($key !== null && $key !== false);
    }

    /**
     * Convert object to JSON.
     *
     * @return string
     */
    public function __toJSON()
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
    public function __toString()
    {
        return $this->__toJSON();
    }

    /**
     * Convert object to an array.
     *
     * @param bool $recursive
     * @return array
     */
    public function __toArray($recursive = false)
    {
        if ($recursive) {
            return Util::convertEasyPostObjectToArray($this->_values);
        }

        return $this->_values;
    }
}

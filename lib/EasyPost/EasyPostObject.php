<?php

namespace EasyPost;

class EasyPostObject implements \ArrayAccess, \Iterator
{
    /**
     * @var string
     */
    protected $_apiKey;

    /**
     * @var array
     */
    protected $_retrieveOptions;

    /**
     * @var array
     */
    protected $_values;

    /**
     * @var array
     */
    protected $_unsavedValues;

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
     * @param string $id
     * @param string $apiKey
     * @param string $parent
     * @param string $name
     */
    public function __construct($id = null, $apiKey = null, $parent = null, $name = null)
    {
        $this->_apiKey = $apiKey;
        $this->_values = [];
        $this->_unsavedValues = [];
        $this->_immutableValues = ['_apiKey', 'id'];
        $this->_parent = $parent;
        $this->_name = $name;

        $this->_retrieveOptions = [];
        if (is_array($id)) {
            foreach ($id as $key => $value) {
                if ($key != 'id') {
                    $this->_retrieveOptions[$key] = $value;
                }
            }
            $id = $id['id'];
        }

        if ($id) {
            $this->id = $id;
        }
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
            } else {
                reset($param);
                $firstKey = key($param);
                $current->_unsavedValues[$firstKey] = $param[$firstKey];
                break;
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
                } else {
                    reset($param);
                    $firstKey = key($param);
                    unset($current->_unsavedValues[$firstKey]);
                    break;
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
            error_log("EasyPost Notice: Undefined property of {$class} instance: {$k}");

            return null;
        }
    }

    /**
     * Construct from.
     *
     * @param array $values
     * @param string $class
     * @param string $apiKey
     * @param string $parent
     * @param string $name
     * @return mixed
     */
    public static function constructFrom($values, $class = null, $apiKey = null, $parent = null, $name = null)
    {
        if ($class === null) {
            $class = get_class();
        }

        $obj = new $class(isset($values['id']) ? $values['id'] : null, $apiKey, $parent, $name);
        $obj->refreshFrom($values, $apiKey);

        return $obj;
    }

    /**
     * Refresh from.
     *
     * @param array $values
     * @param string $apiKey
     * @param bool $partial
     */
    public function refreshFrom($values, $apiKey, $partial = false)
    {
        $this->_apiKey = $apiKey;

        if ($partial) {
            $removed = [];
        } else {
            $removed = array_diff(array_keys($this->_values), array_keys($values));
        }

        foreach ($removed as $k) {
            if (in_array($k, $this->_immutableValues) || in_array($k, $values)) {
                continue;
            }
            unset($this->$k);
        }

        foreach ($values as $k => $v) {
            if ($k == 'id' && $this->id != $v) {
                $this->id = $v;
            }

            if (in_array($k, $this->_immutableValues)) {
                continue;
            }

            $this->_values[$k] = Util::convertToEasyPostObject($v, $apiKey, $this, $k);
        }
        $this->_unsavedValues = [];
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

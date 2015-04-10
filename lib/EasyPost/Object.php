<?php

namespace EasyPost;

class Object implements \ArrayAccess, \Iterator
{
    protected $_apiKey;
    protected $_retrieveOptions;

    protected $_values;
    protected $_unsavedValues;
    protected $_immutableValues;

    private $_parent;
    private $_name;

    public function __construct($id = null, $apiKey = null, $parent = null, $name = null)
    {
        $this->_apiKey = $apiKey;
        $this->_values = array();
        $this->_unsavedValues = array();
        $this->_immutableValues = array('_apiKey', 'id');
        $this->_parent = $parent;
        $this->_name = $name;

        $this->_retrieveOptions = array();
        if (is_array($id)) {
            foreach ($id as $key => $value) {
                if ($key != 'id')
                    $this->_retrieveOptions[$key] = $value;
            }
            $id = $id['id'];
        }

        if ($id) {
            $this->id = $id;
        }
    }

    // Standard accessor magic methods
    public function __set($k, $v)
    {
        $this->_values[$k] = $v;

        $i = 0;
        $current = $this;
        $param = array($k => $v);
        while(true && $i < 99) {
            if(!is_null($current->_parent)) {
                $param = array($current->_name => $param);
                $current = $current->_parent;
            } else {
                reset($param);
                $first_key = key($param);
                $current->_unsavedValues[$first_key] = $param[$first_key];
                break;
            }
            $i++;
        }
    }

    public function __isset($k)
    {
        return isset($this->_values[$k]);
    }

    public function __unset($k)
    {
        if (!in_array($k, $this->_immutableValues)) {
            unset($this->_values[$k]);

            $i = 0;
            $current = $this;
            $param = array($k => $v);
            while(true && $i < 99) {
                if(!is_null($current->_parent)) {
                    $param = array($current->_name => $param);
                    $current = $current->_parent;
                } else {
                    reset($param);
                    $first_key = key($param);
                    unset($current->_unsavedValues[$first_key]);
                    break;
                }
                $i++;
            }
        }
    }

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

    public static function constructFrom($values, $class = null, $apiKey = null, $parent = null, $name = null)
    {
        if ($class === null) {
            $class = get_class();
        }

        $obj = new $class(isset($values['id']) ? $values['id'] : null, $apiKey, $parent, $name);
        $obj->refreshFrom($values, $apiKey);

        return $obj;
    }

    public function refreshFrom($values, $apiKey, $partial = false)
    {
        $this->_apiKey = $apiKey;

        if ($partial) {
            $removed = array();
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
            if (in_array($k, $this->_immutableValues)) {
                continue;
            }
            $this->_values[$k] = Util::convertToEasyPostObject($v, $apiKey, $this, $k);

        }
        $this->_unsavedValues = array();
    }

    // ArrayAccess methods
    public function offsetSet($k, $v)
    {
        $this->$k = $v;
    }

    public function offsetExists($k)
    {
        return array_key_exists($k, $this->_values);
    }

    public function offsetUnset($k)
    {
        unset($this->$k);
    }

    public function offsetGet($k)
    {
        return array_key_exists($k, $this->_values) ? $this->_values[$k] : null;
    }

    // Iterator methods
    public function rewind()
    {
        reset($this->_values);
    }

    public function current()
    {
        return current($this->_values);
    }

    public function key()
    {
        return key($this->_values);
    }

    public function next()
    {
        return next($this->_values);
    }

    public function valid()
    {
        $key = key($this->_values);
        return ($key !== NULL && $key !== FALSE);
    }

    // Output methods
    public function __toJSON()
    {
        if (defined('JSON_PRETTY_PRINT')) {

            return json_encode($this->__toArray(true), JSON_PRETTY_PRINT);
        }

        return json_encode($this->__toArray(true));
    }

    public function __toString()
    {
        return $this->__toJSON();
    }

    public function __toArray($recursive = false)
    {
        if ($recursive) {
            return Util::convertEasyPostObjectToArray($this->_values);
        }

        return $this->_values;
    }
}

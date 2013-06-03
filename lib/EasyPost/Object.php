<?php

namespace EasyPost;

class Object implements \ArrayAccess
{
    protected $_apiKey;
    protected $_retrieveOptions;

    protected $_values;
    protected $_unsavedValues;
    protected $_transientValues;
    protected $_immutableValues;

    public function __construct($id = null, $apiKey = null)
    {
        $this->_apiKey = $apiKey;
        $this->_values = array();
        $this->_unsavedValues = array();
        $this->_transientValues = array();
        $this->_immutableValues = array('_apiKey', 'id');

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

        if (!in_array($k, $this->_immutableValues)) {
            $this->_unsavedValues[$k] = true;
            unset($this->_transientValues[$k]);
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
            $this->_transientValues[$k] = true;
            unset($this->_unsavedValues[$k]);
        }
    }

    public function __get($k)
    {
        if (array_key_exists($k, $this->_values)) {

            return $this->_values[$k];
        } else if (in_array($k, $this->_transientValues)) {
            $class = get_class($this);
            $attrs = join(', ', array_keys($this->_values));
            error_log("EasyPost Notice: Undefined property of {$class} instance: {$k}.");

            return null;
        } else {
            $class = get_class($this);
            error_log("EasyPost Notice: Undefined property of {$class} instance: {$k}");

            return null;
        }
    }

    public static function constructFrom($values, $class = null, $apiKey = null)
    {
        if ($class === null) {
            $class = get_class();
        }

        $obj = new $class(isset($values['id']) ? $values['id'] : null, $apiKey);
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
            $this->_values[$k] = Util::convertToEasyPostObject($v, $apiKey);
            unset($this->_transientValues[$k]);
            unset($this->_unsavedValues[$k]);
        }
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

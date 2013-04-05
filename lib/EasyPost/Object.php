<?php

class EasyPost_Object implements ArrayAccess {
  public static $_permanentAttributes;

  public static function init() {
    self::$_permanentAttributes = array('_apiKey', 'id');
  }

  protected $_apiKey;
  protected $_values;
  protected $_unsavedValues;
  protected $_transientValues;
  protected $_retrieveOptions;

  public function __construct($id=null, $apiKey=null) {
    $this->_apiKey = $apiKey;
    $this->_values = array();
    $this->_unsavedValues = array();
    $this->_transientValues = array();

    $this->_retrieveOptions = array();
    if(is_array($id)) {
      foreach($id as $key => $value) {
        if ($key != 'id')
          $this->_retrieveOptions[$key] = $value;
      }
      $id = $id['id'];
    }

    if($id)
      $this->id = $id;
  }

  // Standard accessor magic methods
  public function __set($k, $v) {
    $this->_values[$k] = $v;
    if(!in_array($k, self::$_permanentAttributes)) {
      $this->_unsavedValues[$k] = true;
      unset($this->_transientValues[$k]);
    }
  }
  public function __isset($k) {
    return isset($this->_values[$k]);
  }
  public function __unset($k) {
    unset($this->_values[$k]);
    $this->_transientValues[$k] = true;
    unset($this->_unsavedValues[$k]);
  }
  public function __get($k) {
    if(array_key_exists($k, $this->_values)) {
      return $this->_values[$k];
    } else if (in_array($k, $this->_transientValues)) {
      $class = get_class($this);
      $attrs = join(', ', array_keys($this->_values));
      error_log("EasyPost Notice: Undefined property of {$class} instance: {$k}.  HINT: The {$k} attribute was set in the past, however.  It was then wiped when refreshing the object with the result returned by EasyPost's API, probably as a result of a save().  The attributes currently available on this object are: {$attrs}");
      return null;
    } else {
      $class = get_class($this);
      error_log("EasyPost Notice: Undefined property of {$class} instance: {$k}");
      return null;
    }
  }

  // ArrayAccess methods
  public function offsetSet($k, $v) {
    $this->$k = $v;
  }
  public function offsetExists($k) {
    return array_key_exists($k, $this->_values);
  }
  public function offsetUnset($k) {
    unset($this->$k);
  }
  public function offsetGet($k) {
    return array_key_exists($k, $this->_values) ? $this->_values[$k] : null;
  }

  public static function scopedConstructFrom($class, $values, $apiKey=null) {
    $obj = new $class(isset($values['id']) ? $values['id'] : null, $apiKey);
    $obj->refreshFrom($values, $apiKey);
    return $obj;
  }

  public static function constructFrom($values, $apiKey=null) {
    $class = get_class();
    return self::scopedConstructFrom($class, $values, $apiKey);
  }

  public function refreshFrom($values, $apiKey, $partial=false) {
    $this->_apiKey = $apiKey;

    if ($partial) {
      $removed = array();
    } else {
      $removed = array_diff(array_keys($this->_values), array_keys($values));
    }

    foreach ($removed as $k) {
      if (in_array($k, self::$_permanentAttributes)) {
        continue;
      }
      unset($this->$k);
    }

    foreach ($values as $k => $v) {
      if (in_array($k, self::$_permanentAttributes)) {
        continue;
      }
      $this->_values[$k] = EasyPost_Util::convertToEasyPostObject($v, $apiKey);
      unset($this->_transientValues[$k]);
      unset($this->_unsavedValues[$k]);
    }
  }

  public function __toJSON() {
    if (defined('JSON_PRETTY_PRINT'))
      return json_encode($this->__toArray(true), JSON_PRETTY_PRINT);
    else
      return json_encode($this->__toArray(true));
  }

  public function __toString() {
    return $this->__toJSON();
  }

  public function __toArray($recursive=false) {
    if ($recursive)
      return EasyPost_Util::convertEasyPostObjectToArray($this->_values);
    else
      return $this->_values;
  }
}

EasyPost_Object::init();

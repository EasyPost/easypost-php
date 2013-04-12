<?php

class EasyPost_Shipment extends EasyPost_Resource {
  public static function constructFrom($values, $class=null, $apiKey=null) {
    $class = get_class();
    return self::constructFrom($values, $class, $apiKey);
  }

  public static function retrieve($id, $apiKey=null) {
    $class = get_class();
    return self::_retrieve($class, $id, $apiKey);
  }

  public static function all($params=null, $apiKey=null) {
    $class = get_class();
    return self::_all($class, $params, $apiKey);
  }

  public static function create($params=null, $apiKey=null) {
    $class = get_class();
    if(!isset($params['shipment']) || !is_array($params['shipment'])) {
      $clone = $params;
      unset($params);
      $params['shipment'] = $clone;
    }
    return self::_create($class, $params, $apiKey);
  }
    
  public function save() {
    $class = get_class();
    return self::_save($class);
  }

  public function rates($params=null, $apiKey=null) {
    $requestor = new EasyPost_Requestor($this->_apiKey);
    $url = $this->instanceUrl() . '/rates';
    list($response, $apiKey) = $requestor->request('get', $url, $params);
    $this->refreshFrom($response, $apiKey, true);
    return $this;
  }

  public function postage_label($params=null) {
    $requestor = new EasyPost_Requestor($this->_apiKey);
    $url = $this->instanceUrl() . '/postage_label';
    list($response, $apiKey) = $requestor->request('get', $url, $params);
    $this->refreshFrom($response, $apiKey);
    return $this;
  }
}
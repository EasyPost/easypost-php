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

  public function buy($params=null) {
    $requestor = new EasyPost_Requestor($this->_apiKey);
    $url = $this->instanceUrl() . '/buy';

    if(isset($params['id']) && (!isset($params['rate']) || !is_array($params['rate']))) {
      $clone = $params;
      unset($params);
      $params['rate'] = $clone;
    }

    list($response, $apiKey) = $requestor->request('post', $url, $params);
    $this->refreshFrom($response, $apiKey, true);
    return $this;
  }

  public function lowest_rate() {
    $lowest_rate = false;
    for($i = 0, $k = count($this->rates); $i < $k; $i++) {
      if(!$lowest_rate || floatval($this->rates[$i]->rate) < floatval($lowest_rate->rate)) {
        $lowest_rate = clone($this->rates[$i]);
      }
    }
    return $lowest_rate;
  }
}
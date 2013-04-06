<?php

class EasyPost_CustomsItem extends EasyPost_Resource {
  public static function constructFrom($values, $apiKey=null) {
    $class = get_class();
    return self::constructFrom($class, $values, $apiKey);
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
    return self::_create($class, $params, $apiKey);
  }
    
  public function save() {
    $class = get_class();
    return self::_save($class);
  }
}
<?php

abstract class EasyPost_Util {

  public static function isList($array) {
    if (!is_array($array))
      return false;
    foreach (array_keys($array) as $k) {
      if (!is_numeric($k))
        return false;
    }
    return true;
  }

  public static function convertEasyPostObjectToArray($values) {
    $results = array();
    foreach ($values as $k => $v) {
      if ($v instanceof EasyPost_Object) {
        $results[$k] = $v->__toArray(true);
      }
      else if (is_array($v)) {
        $results[$k] = self::convertEasyPostObjectToArray($v);
      }
      else {
        $results[$k] = $v;
      }
    }
    return $results;
  }

  public static function convertToEasyPostObject($response, $apiKey) {
    $types = array('Address' => 'EasyPost_Address',
      'ScanForm' => 'EasyPost_ScanForm',
      'CustomsItem' => 'EasyPost_CustomsItem',
      'CustomsInfo' => 'EasyPost_CustomsInfo',
      'Parcel' => 'EasyPost_Parcel',
      'Shipment' => 'EasyPost_Shipment',
      'Rate' => 'EasyPost_Rate',
		  'PostageLabel' => 'EasyPost_PostageLabel');

    $prefixes = array('adr' => 'EasyPost_Address',
      'sf' => 'EasyPost_ScanForm',
      'cstitem' => 'EasyPost_CustomsItem',
      'cstinfo' => 'EasyPost_CustomsInfo',
      'prcl' => 'EasyPost_Parcel',
      'shp' => 'EasyPost_Shipment',
      'rate' => 'EasyPost_Rate',
      'pl' => 'EasyPost_PostageLabel');

    if(self::isList($response)) {
      $mapped = array();
      foreach ($response as $object => $v) {
        if(is_string($object) && isset($types[$object])) {
          $v['object'] = $object;
        }
        array_push($mapped, self::convertToEasyPostObject($v, $apiKey));
      }
      return $mapped;
    } else if(is_array($response)) {
      if(isset($response['object']) && is_string($response['object']) && isset($types[$response['object']])) {
        $class = $types[$response['object']];
      } else if(isset($response['id']) && isset($prefixes[substr($response['id'], 0, strpos($response['id'], "_"))])) {
        $class = $prefixes[substr($response['id'], 0, strpos($response['id'], "_"))];
      } else {
        $class = 'EasyPost_Object';
      }
      return EasyPost_Object::constructFrom($response, $class, $apiKey);
    } else {
      return $response;
    }
  }
}



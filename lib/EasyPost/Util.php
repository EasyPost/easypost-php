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
		  'PostageLabel' => 'EasyPost_PostageLabel',
		  //'BulkLabel' => 'EasyPost_BulkLabel',
      //'Notification' => 'EasyPost_Notification',
		  //'TrackingCode' => 'EasyPost_TrackingCode',
      //'BillingPlan' => 'EasyPost_BillingPlan',
      //'ApiKey' => 'EasyPost_ApiKey',
      'CarrierAccount' => 'EasyPost_CarrierAccount');

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
      $response_key = array_keys($response);
      if(isset($response_key[0]) && is_string($response_key[0]) && isset($types[$response_key[0]])) {
        $response = $response[$response_key[0]];
        $response['object'] = $response_key[0];
      }

      if(isset($response['object']) && is_string($response['object']) && isset($types[$response['object']])) {
        $class = $types[$response['object']];
      } else {
        $class = 'EasyPost_Object';
      }
      return EasyPost_Object::constructFrom($response, $class, $apiKey);
    } else {
      return $response;
    }
  }
}



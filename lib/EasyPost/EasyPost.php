<?php

abstract class EasyPost {
  public static $apiKey;
  public static $apiBase = 'https://www.geteasypost.com/api/v2';
  //public static $apiBase = 'https://easyposttest.herokuapp.com/api/v2';
  // public static $apiBase = 'http://localhost:5000/api/v2';
  public static $apiVersion = "2";
  const VERSION = '1.1';

  public static function getApiKey() {
    return self::$apiKey;
  }

  public static function setApiKey($apiKey) {
    self::$apiKey = $apiKey;
  }

  public static function getApiVersion() {
    return self::$apiVersion;
  }

  public static function setApiVersion($apiVersion) {
    self::$apiVersion = $apiVersion;
  }
}

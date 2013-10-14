<?php

// Require this file if you're not using composer's vendor/autoload

// Required PHP extensions
if (!function_exists('curl_init')) {
  throw new Exception('EasyPost needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('EasyPost needs the JSON PHP extension.');
}

// Config and Utilities
require(dirname(__FILE__) . '/EasyPost/EasyPost.php');
require(dirname(__FILE__) . '/EasyPost/Util.php');
require(dirname(__FILE__) . '/EasyPost/Error.php');

// Guts
require(dirname(__FILE__) . '/EasyPost/Object.php');
require(dirname(__FILE__) . '/EasyPost/Resource.php');
require(dirname(__FILE__) . '/EasyPost/Requestor.php');

// API Resources
require(dirname(__FILE__) . '/EasyPost/Address.php');
require(dirname(__FILE__) . '/EasyPost/ScanForm.php');
require(dirname(__FILE__) . '/EasyPost/CustomsItem.php');
require(dirname(__FILE__) . '/EasyPost/CustomsInfo.php');
require(dirname(__FILE__) . '/EasyPost/Parcel.php');
require(dirname(__FILE__) . '/EasyPost/Rate.php');
require(dirname(__FILE__) . '/EasyPost/PostageLabel.php');
require(dirname(__FILE__) . '/EasyPost/Shipment.php');
require(dirname(__FILE__) . '/EasyPost/Refund.php');
require(dirname(__FILE__) . '/EasyPost/Batch.php');
require(dirname(__FILE__) . '/EasyPost/Tracker.php');
require(dirname(__FILE__) . '/EasyPost/Event.php');

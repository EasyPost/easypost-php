<?php

// Required PHP extensions
if (!function_exists('curl_init')) {
  throw new Exception('EasyPost needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('EasyPost needs the JSON PHP extension.');
}

// Singleton
require(dirname(__FILE__) . '/EasyPost/EasyPost.php');

// Utilities
require(dirname(__FILE__) . '/EasyPost/Util.php');

// Errors
require(dirname(__FILE__) . '/EasyPost/Error.php');
require(dirname(__FILE__) . '/EasyPost/ApiError.php');
require(dirname(__FILE__) . '/EasyPost/ApiConnectionError.php');
require(dirname(__FILE__) . '/EasyPost/AuthenticationError.php');
require(dirname(__FILE__) . '/EasyPost/InvalidRequestError.php');

// Guts
require(dirname(__FILE__) . '/EasyPost/Object.php');
require(dirname(__FILE__) . '/EasyPost/Resource.php');
require(dirname(__FILE__) . '/EasyPost/SingletonResource.php');
require(dirname(__FILE__) . '/EasyPost/Requestor.php');

// API Resources
require(dirname(__FILE__) . '/EasyPost/Address.php');
require(dirname(__FILE__) . '/EasyPost/ScanForm.php');
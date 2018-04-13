<?php

require_once("../lib/easypost.php");

\EasyPost\EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

$tracking_code = "EZ2000000002";
$carrier = "USPS";

// Turn timeouts off
\EasyPost\EasyPost::setConnectTimeout(0);
\EasyPost\EasyPost::setResponseTimeout(0);

// Creation of test tracker should not timeout
$tracker = \EasyPost\Tracker::create(array('tracking_code' => $tracking_code, 'carrier' => $carrier));
var_dump($tracker->id);                      // This is random

// Set timeouts to 1ms
\EasyPost\EasyPost::setConnectTimeout(1);
\EasyPost\EasyPost::setResponseTimeout(1);

// Creation of test tracker should timeout
// This is expected to raise an EasyPost\Error
$tracker = \EasyPost\Tracker::create(array('tracking_code' => $tracking_code, 'carrier' => $carrier));

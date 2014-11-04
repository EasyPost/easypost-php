<?php

require_once("../lib/easypost.php");

\EasyPost\EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

$tracking_code = "EZ2000000002";
$carrier = "USPS";

$tracker = \EasyPost\Tracker::create(array('tracking_code' => $tracking_code, 'carrier' => $carrier));

print_r($tracker);

$tracker2 = \EasyPost\Tracker::retrieve($tracker->id);

print_r($tracker2);

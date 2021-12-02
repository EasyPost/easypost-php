<?php

require_once("../lib/easypost.php");

\EasyPost\EasyPost::setApiKey(getenv('API_KEY'));

$shipment = \EasyPost\Shipment::create_from_tracking_code(array("tracking_code" => "1Z0X89330378901784", "options" => array("residential_to_address" => 1)));

print_r($shipment->rates);

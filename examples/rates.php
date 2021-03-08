<?php

require_once("../lib/easypost.php");

\EasyPost\EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

// get shipment rates (assumes you have a shipment object)
// this is optional as rates are added to the obj when it's created if addresses and parcel are present
if (count($shipment->rates) > 0) {
    $shipment->get_rates();
    print_r($shipment);
}

// retrieve the shipment's lowest rate
$lowest_rate = \EasyPost\Rate::retrieve($shipment->lowest_rate());
print_r($lowest_rate);

// retrieve a single shipment's rate by ID
$rate = \EasyPost\Rate::retrieve("rate_123...");
print_r($rate);

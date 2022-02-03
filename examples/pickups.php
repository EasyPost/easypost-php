<?php

require_once("../lib/easypost.php");

\EasyPost\EasyPost::setApiKey(getenv('API_KEY'));

$to_address = \EasyPost\Address::create([
    "name"    => "Jeff Greenstein",
    "street1" => "2 8th St",
    "city"    => "Hermosa Beach",
    "state"   => "CA",
    "zip"     => "90254",
    "phone"   => "310-456-7890"
]);
$from_address = \EasyPost\Address::create([
    "company" => "EasyPost",
    "street1" => "164 Townsend",
    "street2" => "#1",
    "city"    => "San Francisco",
    "state"   => "CA",
    "zip"     => "94107",
    "phone"   => "415-379-7678"
]);
$parcel = \EasyPost\Parcel::create([
    "height" => 10,
    "length" => 15,
    "width" => 5,
    "weight" => 32
]);
$shipment = \EasyPost\Shipment::create([
    "to_address"   => $to_address,
    "from_address" => $from_address,
    "parcel"       => $parcel
]);
$shipment->buy($shipment->lowest_rate(['UPS']));
$shipment->insure(['amount' => 100]);

echo $shipment->id . "\n";

$pickup = \EasyPost\Pickup::create([
    "address" => $from_address,
    "shipment" => $shipment,
    "reference" => $shipment->id,
    "min_datetime" => date("Y-m-d H:i:s", strtotime('+1 day')),
    "max_datetime" => date("Y-m-d H:i:s", strtotime('+25 hours')),
    "is_account_address" => false,
    "instructions" => "Will be next to garage"
]);

$pickup->buy(['carrier' => 'UPS', 'service' => 'Future-day Pickup']);
echo "Confirmation: " . $pickup->confirmation . "\n";

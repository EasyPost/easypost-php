<?php

require_once("../lib/easypost.php");

\EasyPost\EasyPost::setApiKey(getenv('API_KEY'));

$shipment = \EasyPost\Shipment::create([
    'to_address' => [
        "name"    => "Dr. Steve Brule",
        "street1" => "179 N Harbor Dr",
        "city"    => "Redondo Beach",
        "state"   => "CA",
        "zip"     => "90277",
        "phone"   => "310-808-5243"
    ],
    'from_address' => [
        "company" => "EasyPost",
        "street1" => "118 2nd Street",
        "street2" => "4th Floor",
        "city"    => "San Francisco",
        "state"   => "CA",
        "zip"     => "94105",
        "phone"   => "415-456-7890"
    ],
    'parcel' => [
        'length' => 9,
        'width' => 6,
        'height' => 2,
        'weight' => 10
    ]
]);

echo $shipment->id;

$shipment->buy($shipment->lowest_rate("USPS"));

$shipment->insure(['amount' => 100]);

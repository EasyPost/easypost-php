<?php

require_once("../vendor/autoload.php");
\EasyPost\EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

$to_address = \EasyPost\Address::create(
    array(
        "name"    => "Dirk Diggler",
        "street1" => "388 Townsend St",
        "street2" => "Apt 20",
        "city"    => "San Francisco",
        "state"   => "CA",
        "zip"     => "94107",
        "phone"   => "415-456-7890"
    )
);
$from_address = \EasyPost\Address::create(
    array(
        "company" => "Simpler Postage Inc",
        "street1" => "764 Warehouse Ave",
        "city"    => "Kansas City",
        "state"   => "KS",
        "zip"     => "66101",
        "phone"   => "620-123-4567"
    )
);
$parcel = \EasyPost\Parcel::create(
    array(
        "predefined_package" => "LargeFlatRateBox",
        "weight" => 76.9
    )
);
$shipment = \EasyPost\Shipment::create(
    array(
        "to_address"   => $to_address,
        "from_address" => $from_address,
        "parcel"       => $parcel
    )
);

$shipment->buy($shipment->lowest_rate());

echo $shipment->postage_label->label_url;

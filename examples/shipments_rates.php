<?php

require_once("../lib/easypost.php");

EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

// create addresses
$to_address_params = array("name" => "Sawyer Bateman",
                        "street1" => "388 Townsend St",
                        "street2" => "Apt 30",
                        "city" => "San Francisco",
                        "state" => "CA",
                        "zip" => "94107");
$to_address = EasyPost_Address::create($to_address_params);

$from_address_params = array("name" => "Jon Calhoun",
                        "street1" => "388 Townsend St",
                        "street2" => "Apt 20",
                        "city" => "San Francisco",
                        "state" => "CA",
                        "zip" => "94107");
$from_address = EasyPost_Address::create($from_address_params);


// create parcel
$parcel_params = array("length" => 20.2,
    "width" => 10.9,
    "height" => 5,
    "predefined_package" => null,
    "weight" => 14.8
);
$parcel = EasyPost_Parcel::create($parcel_params);


// create shipment
$shipment_params = array("from_address" => $from_address,
    "to_address" => $to_address,
    "parcel" => $parcel
);
$shipment = EasyPost_Shipment::create($shipment_params);
print_r($shipment);

// get shipment rates - optional, rates are added to the obj when it's created if addresses and parcel are present
if(count($shipment->rates) === 0) {
    $shipment->rates();
    print_r($shipment);
}

// retrieve one rate
$rate = EasyPost_Rate::retrieve($shipment->lowest_rate());
print_r($rate);

// create rates the other way
$created_rates = EasyPost_Rate::create($shipment);
print_r($created_rates);


print_r(EasyPost_Shipment::retrieve($shipment));










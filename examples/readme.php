<?php

require_once("../lib/easypost.php");

EasyPost::setApiKey("cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi");
    
$to_param = array("street1" => "388 Townsend St", "street2" => "Apt 20", "city" => "San Francisco", "state" => "CA", "zip" => "94107");
$from_param = array("company" => "Simpler Postage Inc", "street1" => "764 Warehouse Ave", "street2" => "", "city" => "Kansas City", "state" => "KS", "zip" => "66101", "phone" => "620-123-4567");
$parcel_param = array("predefined_package" => "LargeFlatRateBox", "weight" => 100.0); // weight in ounces

$to_address = EasyPost_Address::create($to_param);
$from_address = EasyPost_Address::create($from_param);
$parcel = EasyPost_Parcel::create($parcel_param);

$shipment = EasyPost_Shipment::create(array(
  "to_address" => $to_address,
  "from_address" => $from_address,
  "parcel" => $parcel
));

// print_r($shipment->rates);
   
$shipment->buy($shipment->lowest_rate());

echo $shipment->postage_label->label_url;
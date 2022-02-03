<?php

require_once("../lib/easypost.php");
\EasyPost\EasyPost::setApiKey(getenv('API_KEY'));

// create addresses
$from_address = [
    "company" => "EasyPost",
    "street1" => "388 Townsend St",
    "city"    => "San Francisco",
    "state"   => "CA",
    "zip"     => "94107-8273",
    "phone"   => "415-456-7890"
];
$to_address = [
    "name"    => "Ronald",
    "street1" => "6258 Amesbury St",
    "city"    => "San Diego",
    "state"   => "CA",
    "zip"     => "92114"
];
$parcel = [
    "predefined_package" => 'Parcel',
    "weight"             => 22.9
];

// create shipment and buy
$shipments = [];
$shipment = \EasyPost\Shipment::create([
    "to_address"   => $to_address,
    "from_address" => $from_address,
    "parcel"       => $parcel,
]);
$shipment->buy(["rate" => $shipment->lowest_rate('usps')]);
$shipments[] = $shipment;

// create a scan form
$scan_form = \EasyPost\ScanForm::create([
    "shipments" => $shipments
]);

// inspect scanform
var_dump($scan_form->id);
var_dump($scan_form->tracking_codes[0] == $shipment->tracking_code);

// retrieve a copy of the scan form
$scan_form2 = \EasyPost\ScanForm::retrieve($scan_form->id);
var_dump($scan_form2->id);

// index scan forms
$scan_forms = \EasyPost\ScanForm::all(["page_size" => 2]);
var_dump($scan_forms["scan_forms"][0]->id);

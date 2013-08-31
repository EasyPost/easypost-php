<?php

require_once("../lib/easypost.php");

\EasyPost\EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

// create addresses
$from_address = array(
    "company" => "EasyPost",
    "street1" => "388 Townsend St",
    "city"    => "San Francisco",
    "state"   => "CA",
    "zip"     => "94107-8273",
    "phone"   => "415-456-7890"
);
$parcel = array(
    "predefined_package" => 'Parcel',
    "weight"             => 22.9
);

// in your application orders will likely
// come from an external data source
$orders = array(
    array(
        "address"  => array(
            "name"    => "Ronald",
            "street1" => "6258 Amesbury St",
            "city"    => "San Diego",
            "state"   => "CA",
            "zip"     => "92114"
        ),
        "reference"   => "123456786",
        "carrier"     => "USPS",
        "service"     => "Priority"
    ),
    array(
        "address"  => array(
            "name"    => "Hamburgler",
            "street1" => "8308 Fenway Rd",
            "city"    => "Bethesda",
            "state"   => "MD",
            "zip"     => "20817"
        ),
        "reference"   => "123456787",
        "carrier"     => "USPS",
        "service"     => "Priority"
    ),
    array(
        "address"  => array(
            "name"    => "Grimace",
            "street1" => "10 Wall St",
            "city"    => "Burlington",
            "state"   => "MA",
            "zip"     => "01803"
        ),
        "reference"   => "123456788",
        "carrier"     => "USPS",
        "service"     => "Priority"
    ),
    array(
        "address"  => array(
            "name"    => "Cosmc",
            "street1" => "3315 W Greenway Rd",
            "city"    => "Phoenix",
            "state"   => "AZ",
            "zip"     => "85053"
        ),
        "reference"   => "123456789",
        "carrier"     => "USPS",
        "service"     => "Express"
    )
);

// get a list of all my batches
// $all = \EasyPost\Batch::all();
// print_r($all);

// retrieve a batch
// $batch = \EasyPost\Batch::retrieve('batch_0SLoY64K');

// create shipment batch
$shipments = array();
for($i = 0, $j = count($orders); $i < $j; $i++) {
    $shipments[] = array(
        "to_address"   => $orders[$i]["address"],
        "from_address" => $from_address,
        "parcel"       => $parcel,
        "reference"    => $orders[$i]["reference"],
        "carrier"      => $orders[$i]["carrier"],
        "service"      => $orders[$i]["service"]
    );
}

$batch = \EasyPost\Batch::create(array('shipments' => $shipments));

// asynchronous creation means you can send us up to
// 1000 shipments at once, but you'll have to wait
// for the shipments to be created before you can continue
while($batch->status->created != count($orders)) {
    sleep(5);
    $batch->refresh();
    if($batch->status->creation_failed != 0) {
        throw new \EasyPost\Error('One of your batch shipments was unable to be created. Please manually retrieve and review your batch.');
    }
}

// if creation_failed == 0 and the while loop above ends
// we know that all shipments have been created
$batch->buy();

// asyncronous purchasing means we have to watch
// for when all labels have been purchased
while($batch->status->postage_purchased != count($orders)) {
    sleep(5);
    $batch->refresh();
    if($batch->status->postage_purchase_failed != 0) {
        throw new \EasyPost\Error('One of your batch shipments was unable to be purchased. Please manually retrieve and review your batch.');
    }
}

// generate a consolidated file containing all batch labels
$batch->label(array("file_format" => "pdf"));

while(empty($batch->label_url)) {
    sleep(5);
    $batch->refresh();
}

print_r($batch);

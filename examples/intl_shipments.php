<?php

// require_once("../vendor/autoload.php");
require_once("../lib/easypost.php");

\EasyPost\EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

// create addresses
$sf_address_params = array(
    "name"    => "Jon Calhoun",
    "street1" => "388 Townsend St",
    "street2" => "Apt 20",
    "city"    => "San Francisco",
    "state"   => "CA",
    "zip"     => "94107-8273",
    "phone"   => "415-456-7890"
);
$sf2_address_params = array(
    "name"    => "Dirk Diggler",
    "street1" => "63 Ellis Street",
    // "street2" => "Suite 1290",
    "city"    => "San Francisco",
    "state"   => "CA",
    "zip"     => "94102",
    "phone"   => "415-482-2937"
);
$canada_address_params = array(
    "name"    => "Sawyer Bateman",
    "street1" => "1A Larkspur Cres",
    "city"    => "St. Albert",
    "state"   => "AB",
    "zip"     => "t8n2m4",
    "country" => "CA",
    "phone"   => "780-252-8464"
);
$china_address_params = array(
    "name"    => "姚明",
    "street1" => "香港东路6号，5号楼，8号室",
    "city"    => "青岛市",
    "zip"     => "201900",
    "phone"   => "21-7283-8264",
    "country" => "CN"
);
$uk_address_params = array(
    "name" => "Queen Elizabeth",
    "street1" => "Buckingham Palace",
    "phone" => "+44 20 7930 4832",
    "city" => "London",
    "zip" => "SW1A 1AA",
    "country" => "GB"
);

$from_address = \EasyPost\Address::create($sf_address_params);
$to_address = \EasyPost\Address::create($canada_address_params);

// create parcel
$parcel_params = array(
    "length" => 20.2,
    "width" => 10.9,
    "height" => 5,
    "weight" => 14.8
);
$parcel = \EasyPost\Parcel::create($parcel_params);


// customs info form
$customs_item_params = array(
    "description"      => "Many, many EasyPost stickers.",
    "hs_tariff_number" => 123456,
    "origin_country"   => "US",
    "quantity"         => 1,
    "value"            => 879.47,
    "weight"           => 14
);
$customs_item = \EasyPost\CustomsItem::create($customs_item_params);

$customs_info_params = array(
    "customs_certify"      => true,
    "customs_signer"       => "Borat Sagdiyev",
    "contents_type"        => "gift",
    "contents_explanation" => "", // only necessary for contents_type=other
    "eel_pfc"              => "NOEEI 30.37(a)",
    "non_delivery_option"  => "abandon",
    "restriction_type"     => "none",
    "restriction_comments" => "",
    "customs_items"        => array($customs_item)
);
$customs_info = \EasyPost\CustomsInfo::create($customs_info_params);

// create shipment
$shipment_params = array(
    "from_address" => $from_address,
    "to_address"   => $to_address,
    "parcel"       => $parcel,
    "customs_info" => $customs_info
);
$shipment = \EasyPost\Shipment::create($shipment_params);

$shipment->buy(array('rate' => $shipment->lowest_rate('usps'), 'insurance' => 249.99));
// $shipment->buy(array('rate' => $shipment->lowest_rate(array('ups', 'fedex'))));
// $shipment->buy($shipment->lowest_rate(null, 'PriorityMailInternational'));

// Refund specific shipment example
// $shipment = \EasyPost\Shipment::retrieve('shp_fX5JFpdF');
// $shipment->refund();

print_r($shipment);

echo $shipment->postage_label->label_url;

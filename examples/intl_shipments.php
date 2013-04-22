<?php

require_once("../lib/easypost.php");

EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

// create addresses
$to_address_params = array("name" => "Sawyer Bateman",
                        "street1" => "1A Larkspur Cres",
                        "street2" => "",
                        "city" => "St. Albert",
                        "state" => "AB",
                        "zip" => "t8n2m4",
                        "country" => "CA");
$to_address = EasyPost_Address::create($to_address_params);

$from_address_params = array("name" => "Jon Calhoun",
                        "street1" => "388 Townsend St",
                        "street2" => "Apt 20",
                        "city" => "San Francisco",
                        "state" => "CA",
                        "zip" => "94107",
                        "phone" => "415-456-7890");
$from_address = EasyPost_Address::create($from_address_params);


// create parcel
$parcel_params = array("length" => 20.2,
    "width" => 10.9,
    "height" => 5,
    "predefined_package" => null,
    "weight" => 14.8
);
$parcel = EasyPost_Parcel::create($parcel_params);


// customs info form
$customs_item_params = array("description" => "Many, many EasyPost stickers.",
    "hs_tariff_number" => 123456,
    "origin_country" => "US",
    "quantity" => 1,
    "value" => 879.47,
    "weight" => 14
);
$customs_item = EasyPost_CustomsItem::create($customs_item_params);

$customs_info_params = array("integrated_form_type" => "form_2976",
    "customs_certify" => true,
    "customs_signer" => "Borat Sagdiyev",
    "contents_type" => "gift",
    "contents_explanation" => "You wouldn't believe how many EasyPost stickers I packed into this parcel.",
    "eel_pfc" => "NOEEI 30.37(a).",
    "non_delivery_option" => "abandon",
    "restriction_type" => "none",
    "restriction_comments" => "",
    "customs_items" => array($customs_item)
);
$customs_info = EasyPost_CustomsInfo::create($customs_info_params);


// create shipment
$shipment_params = array("from_address" => $from_address,
    "to_address" => $to_address,
    "parcel" => $parcel,
    "customs_info" => $customs_info
);
$shipment = EasyPost_Shipment::create($shipment_params);

$shipment->buy($shipment->lowest_rate());

print_r($shipment);

echo $shipment->postage_label->label_url;
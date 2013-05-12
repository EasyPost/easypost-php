<?php

require_once("../vendor/autoload.php");
\EasyPost\EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

// create addresses
$canada_address_params = array("name"    => "Sawyer Bateman",
                               "street1" => "1A Larkspur Cres",
                               "city"    => "St. Albert",
                               "state"   => "AB",
                               "zip"     => "t8n2m4",
                               "country" => "CA",
                               "phone"   => "780-252-8464");
$sf_address_params = array("name"    => "Jon Calhoun",
                           "street1" => "388 Townsend St",
                           "street2" => "Apt 20",
                           "city"    => "San Francisco",
                           "state"   => "CA",
                           "zip"     => "94107-8273",
                           "phone"   => "415-456-7890");
$from_address = \EasyPost\Address::create($sf_address_params);
$to_address = \EasyPost\Address::create($canada_address_params);


// create parcel
$parcel_params = array("length" => 20.2,
                       "width"  => 10.9,
                       "height" => 5,
                       "weight" => 14.8
);
$parcel = \EasyPost\Parcel::create($parcel_params);


// customs info form
$customs_item_params = array("description"      => "Many, many EasyPost stickers.",
                             "hs_tariff_number" => 123456,
                             "origin_country"   => "US",
                             "quantity"         => 1,
                             "value"            => 879.47,
                             "weight"           => 14
);
$customs_item = \EasyPost\CustomsItem::create($customs_item_params);

$customs_info_params = array("integrated_form_type" => "form_2976",
                             "customs_certify"      => true,
                             "customs_signer"       => "Borat Sagdiyev",
                             "contents_type"        => "gift",
                             "contents_explanation" => "", // only necessary for contents_type=other
                             "eel_pfc"              => "NOEEI 30.36",
                             "non_delivery_option"  => "abandon",
                             "restriction_type"     => "none",
                             "restriction_comments" => "",
                             "customs_items"        => array($customs_item)
);
$customs_info = \EasyPost\CustomsInfo::create($customs_info_params);

// create shipment
$shipment_params = array("from_address" => $from_address,
                         "to_address"   => $to_address,
                         "parcel"       => $parcel,
                         "customs_info" => $customs_info
);
$shipment = \EasyPost\Shipment::create($shipment_params);

$shipment->buy($shipment->lowest_rate());

// this is necessary for the time being to get tracking_code into $shipment until we fix buy to also refresh the obj
$shipment = \EasyPost\Shipment::retrieve($shipment->id);

print_r($shipment);

echo $shipment->postage_label->label_url;

<?php

require_once("../lib/easypost.php");

\EasyPost\EasyPost::setApiKey(getenv('API_KEY'));

$sf = [
    "name"    => "EasyPost.com",
    "street1" => "164 Townsend St #1",
    "street2" => "",
    "city"    => "San Francisco",
    "state"   => "CA ",
    "zip"     => "94107",
    "phone"   => "415-456-7890"
];

$maryland = [
    "name" => "Maryland",
    "street1" => "8308 Fenway Rd",
    "city" => "Bethesda",
    "state" => "MD",
    "zip" => "20817",
    "phone"   => "415-482-2937",
    "email" => "sawyer@example.com"
];

// overall customs info form
$overall_customs_item_params = [
    "description"      => "Many, many EasyPost stickers.",
    "hs_tariff_number" => 123456,
    "origin_country"   => "US",
    "quantity"         => 1,
    "value"            => 879.47,
    "weight"           => 14
];
$overall_customs_item = \EasyPost\CustomsItem::create($overall_customs_item_params);

$overall_customs_info_params = [
    "customs_certify"      => true,
    "customs_signer"       => "Borat Sagdiyev",
    "contents_type"        => "gift",
    "contents_explanation" => "", // only necessary for contents_type=other
    "eel_pfc"              => "NOEEI 30.37(a)",
    "non_delivery_option"  => "abandon",
    "restriction_type"     => "none",
    "restriction_comments" => "",
    "customs_items"        => [$overall_customs_item]
];
$overall_customs_info = \EasyPost\CustomsInfo::create($overall_customs_info_params);

// specific customs info form
$specific_customs_item_params = [
    "description"      => "Many, many EasyPost stickers.",
    "hs_tariff_number" => 123456,
    "origin_country"   => "US",
    "quantity"         => 1,
    "value"            => 879.47,
    "weight"           => 14
];
$specific_customs_item = \EasyPost\CustomsItem::create($specific_customs_item_params);

$specific_customs_info_params = [
    "customs_certify"      => true,
    "customs_signer"       => "Borat Sagdiyev",
    "contents_type"        => "gift",
    "contents_explanation" => "", // only necessary for contents_type=other
    "eel_pfc"              => "NOEEI 30.37(a)",
    "non_delivery_option"  => "abandon",
    "restriction_type"     => "none",
    "restriction_comments" => "",
    "customs_items"        => [$specific_customs_item]
];
$specific_customs_info = \EasyPost\CustomsInfo::create($specific_customs_info_params);

$order = \EasyPost\Order::create([
    "from_address" => $sf,
    "to_address" => $maryland,
    "customs_info" => $overall_customs_info, // Customs info on the order should reflect the total of all packages
    "shipments" => [
        [
            "parcel" => ["length" => 12.0, "width" => 10.5, "height" => 6.8, "weight" => 12],
            "options" => ["cod_amount" => 14.99],
            "customs_info" => $specific_customs_info // Customs info on each shipment should reflect the contents of that shipment
        ],
        [
            "parcel" => ["length" => 11.9, "width" => 10.0, "height" => 7.3, "weight" => 18],
            "options" => ["cod_amount" => 9.56],
            "customs_info" => $specific_customs_info // Customs info on each shipment should reflect the contents of that shipment
        ],
    ],
]);

$order->buy(["carrier" => "UPS", "service" => "Ground"]);

print_r($order->id);

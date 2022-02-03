<?php

require_once("../vendor/autoload.php");
\EasyPost\EasyPost::setApiKey(getenv('API_KEY'));

// CustomsItem: create
$params = [
    "description"      => "I like your dog, he's very nice.",
    "hs_tariff_number" => 123456,
    "origin_country"   => "US",
    "quantity"         => 2,
    "value"            => 1.23,
    "weight"           => 14
];
$customs_item = \EasyPost\CustomsItem::create($params);

// retrieve
$retrieved = \EasyPost\CustomsItem::retrieve($customs_item->id);
print_r($retrieved);

// all
$all = \EasyPost\CustomsItem::all();
//print_r($all);
/*
for($i = 0, $k = count($all); $i < $k; $i++) {
  print_r(\EasyPost\CustomsItem::retrieve($all[$i]->id));
}
*/

// CustomsInfo: create
$params = [
    "integrated_form_type" => "form_2976",
    "customs_certify"      => true,
    "customs_signer"       => "Borat Sagdiyev",
    "contents_type"        => "gift",
    "contents_explanation" => "",
    "eel_pfc"              => "NOEEI 30.37(a)",
    "non_delivery_option"  => "abandon",
    "restriction_type"     => "none",
    "restriction_comments" => "",
    "customs_items"        => [$customs_item]
];
$customs_info = \EasyPost\CustomsInfo::create($params);

// retrieve
$retrieved = \EasyPost\CustomsInfo::retrieve($customs_info->id);
print_r($retrieved);

// all
$all = \EasyPost\CustomsInfo::all();
//print_r($all);

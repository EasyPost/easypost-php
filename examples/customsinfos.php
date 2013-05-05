<?php

require_once("../lib/easypost.php");

EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

// CustomsItem: create
$params = array("description" => "I like your dog, he's vry nice.",
    "hs_tariff_number" => 123456,
    "origin_country" => "US",
    "quantity" => 2,
    "value" => 1.23,
    "weight" => 14
);
$customs_item = EasyPost_CustomsItem::create($params);

// retrieve
$retrieved = EasyPost_CustomsItem::retrieve($customs_item->id);
print_r($retrieved);

// all
$all = EasyPost_CustomsItem::all();
//print_r($all);
/*
for($i = 0, $k = count($all); $i < $k; $i++) {
  print_r(EasyPost_CustomsItem::retrieve($all[$i]->id));
}
*/

// CustomsInfo: create
$params = array("integrated_form_type" => "form_2976",
    "customs_certify" => true,
    "customs_signer" => "Borat Sagdiyev",
    "contents_type" => "gift",
    "contents_explanation" => "",
    "eel_pfc" => "NOEEI 30.37(a)",
    "non_delivery_option" => "abandon",
    "restriction_type" => "none",
    "restriction_comments" => "",
    "customs_items" => array($customs_item)
);
$customs_info = EasyPost_CustomsInfo::create($params);

// retrieve
$retrieved = EasyPost_CustomsInfo::retrieve($customs_info->id);
print_r($retrieved);

// all
$all = EasyPost_CustomsInfo::all();
//print_r($all);





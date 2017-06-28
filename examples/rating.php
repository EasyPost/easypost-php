<?php

require_once("../lib/easypost.php");

\EasyPost\EasyPost::setApiKey('APIKEYSTANDALONERATINGACTIVATED');

//PARAMATER CREATION HAS TO BE DONE IN PLACE
$rt = \EasyPost\Rating::create(array("to_address" => array(
	"name"    => "Ronald Barrison",
	"street1" => "6258 Amesbury St",
	"city"    => "San Diego",
	"state"   => "CA",
	"country" => "US",
	"zip"     => "92114"
),
    "from_address" => array(
    "street1" => "417 MONTGOMERY ST",
    "street2" => "FL 5",
    "city"    => "San Francisco",
    "state"   => "CA",
    "zip"     => "94104",
    "country" => "US",
    "company" => "EasyPost",
    "phone"   => "415-123-4567"
),
    "parcels" => array(array(
	"length" => 20.2,
    "width" => 10.9,
    "height" => 5,
    "weight" => 65.9
)),
    "carrier_accounts" => array("CARRIERACCOUNTID1","CARRIERACCOUNTID2")
));

print($rt);
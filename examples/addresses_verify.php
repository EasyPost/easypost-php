<?php

// require_once("../vendor/autoload.php");
require_once("../lib/easypost.php");
\EasyPost\EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

// create address
$address_params = array(
    "verify"  =>  array("delivery"),
    "street1" => "118 2 streat",
    "street2" => "FL 4",
    "city"    => "San Francisco",
    "state"   => "CA",
    "zip"     => "94105",
    "country" => "US",
    "company" => "EasyPost",
    "phone"   => "415-123-4567"
);

$address = \EasyPost\Address::create($address_params);
echo $address->street1 . "\n";

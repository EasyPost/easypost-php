<?php

// require_once("../vendor/autoload.php");
require_once("../lib/easypost.php");
\EasyPost\EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

try {
    // create address
    $address_params = array(
        "verify_strict"  =>  array("delivery"),
        "street1"        => "UNDELIEVRABLE ST",
        "street2"        => "FL 4",
        "city"           => "San Francisco",
        "state"          => "CA",
        "zip"            => "94105",
        "country"        => "US",
        "company"        => "EasyPost",
        "phone"          => "415-123-4567"
    );

    $address = \EasyPost\Address::create($address_params);
} catch (Exception $e) {
    echo "Status: " . $e->getHttpStatus() . ": ";
    echo $e->getMessage() . "\n";
    exit();
}

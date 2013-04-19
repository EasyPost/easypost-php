<?php

require_once("../lib/easypost.php");
EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

try {
    // create address
    $address_params = array("name" => "Sawyer Bateman",
                            "street1" => "388 Townsend St",
                            "street2" => "Apt 20",
                            "city" => "San Francisco",
                            "state" => "CA",
                            "zip" => "94107",
                            "country" => "US");

    $address = EasyPost_Address::create($address_params);
    print_r($address);

    // retrieve
    $retrieved_address = EasyPost_Address::retrieve($address->id);
    print_r($retrieved_address);

    // verify
    $verified_address = $address->verify();
    print_r($verified_address);

    // all
    $all = EasyPost_Address::all();
    print_r($all);

} catch(Exception $e) {
    echo "Status: " . $e->getHttpStatus() . ":\n";
    exit($e->getMessage());
}


<?php

require_once("../vendor/autoload.php");
\EasyPost\EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

try {
    // create format 1
    $from_address = array("name"    => "Jon Calhoun",
                          "street1" => "388 Townsend St",
                          "street2" => "Apt 20",
                          "city"    => "San Francisco",
                          "state"   => "CA",
                          "zip"     => "94107");
    $tracking_codes = "123456,123455,123454,123453,123452";

    $scan_form = \EasyPost\ScanForm::create(array("from_address" => $from_address, "tracking_codes" => $tracking_codes));
    print_r($scan_form);

    // create from address object and tracking code array
    $from_address_obj = \EasyPost\Address::create($from_address);
    $tracking_code_array = array(123456, 123455, 123454, 123453, 123452);

    $scan_form_2 = \EasyPost\ScanForm::create(array("from_address" => $from_address_obj, "tracking_codes" => $tracking_code_array));
    print_r($scan_form_2);

    // retrieve
    $retrieved = \EasyPost\ScanForm::retrieve($scan_form_2->id);
    print_r($retrieved);

    // all
    $all = \EasyPost\ScanForm::all();
    //print_r($all);

} catch (Exception $e) {
    echo "Status: " . $e->getHttpStatus() . ":\n";
    exit($e->getMessage());
}

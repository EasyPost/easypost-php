<?php

require_once("../lib/easypost.php");

EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

// create format 1
$from = array("name" => "Jon Calhoun",
              "street1" => "388 Townsend St",
              "street2" => "Apt 20",
              "city" => "San Francisco",
              "state" => "CA",
              "zip" => "94107");
$tracking_codes = "123456,123455,123454,123453,123452";

$scan_form = EasyPost_ScanForm::create(array("from" => $from, "tracking_codes" => $tracking_codes));
print_r($scan_form);


// create format 2
$from_address = EasyPost_Address::create($from);
//$tracking_code_array = array(123456,123455,123454,123453,123452);

$scan_form_2 = EasyPost_ScanForm::create(array("from" => $from_address, "tracking_codes" => $tracking_codes));
print_r($scan_form_2);

// retrieve
$retrieved = EasyPost_ScanForm::retrieve($scan_form_2->id);
print_r($retrieved);


// all
$all = EasyPost_ScanForm::all();
print_r($all);
/*
for($i = 0, $k = count($all); $i < $k; $i++) {
  print_r(EasyPost_ScanForm::retrieve($all[$i]->id));
}
*/


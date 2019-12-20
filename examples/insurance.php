<?php

require_once("../lib/easypost.php");

\EasyPost\EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

$tracking_code = "EZ2000000002";
$carrier = "USPS";
$amount = 101.00;

$to_address = array(
  "name"    => "Dr. Steve Brule",
  "street1" => "179 N Harbor Dr",
  "city"    => "Redondo Beach",
  "state"   => "CA",
  "zip"     => "90277",
  "phone"   => "310-808-5243"
);

$from_address = array(
  "company" => "EasyPost",
  "street1" => "118 2nd Street",
  "street2" => "4th Floor",
  "city"    => "San Francisco",
  "state"   => "CA",
  "zip"     => "94105",
  "phone"   => "415-456-7890"
);

$insurance = \EasyPost\Insurance::create(
    array(
      'to_address' => $to_address,
      'from_address' => $from_address,
      'amount'    => $amount,
      'tracking_code' => $tracking_code,
      'carrier'   => $carrier
    )
);

var_dump($insurance->id);                      // This is random

// retrieve insurance by id
$insurance2 = \EasyPost\Insurance::retrieve($insurance->id);

var_dump($insurance2->id);                     // Should be the same as above

// retrieve all insurances
$insurances = \EasyPost\Insurance::all();

var_dump(count($insurances["insurances"]));    // Should be 30
var_dump($insurances["has_more"]);             // Should be true, unless the count() isn't 30
var_dump($insurances["insurances"][0]->id);    // Should be an insurance ID

// create another test insurance
$insurance3 = \EasyPost\Insurance::create(
    array(
      'to_address' => $to_address,
      'from_address' => $from_address,
      'amount'    => $amount,
      'tracking_code' => $tracking_code,
      'carrier'   => $carrier
    )
);

var_dump($insurance3->id);                     // This is random

// retrieve all created since 'insurance'
$insurances2 = \EasyPost\Insurance::all(array('after_id' => $insurance->id));

var_dump(count($insurances2["insurances"]));   // Should be 1
var_dump($insurances2["has_more"]);            // Should be false
var_dump($insurances2["insurances"][0]->id);   // Should be the same as the id for insurance3

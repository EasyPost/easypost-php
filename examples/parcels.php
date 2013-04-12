<?php

require_once("../lib/easypost.php");

EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

// Parcel: create
$params = array("length" => 20.2,
    "width" => 10.9,
    "height" => 5,
    "predefined_package" => null,
    "weight" => 14.8
);
$parcel = EasyPost_Parcel::create($params);

// retrieve
$retrieved = EasyPost_Parcel::retrieve($parcel->id);
print_r($retrieved);

// all
$all = EasyPost_Parcel::all();
print_r($all);
/*
for($i = 0, $k = count($all); $i < $k; $i++) {
  print_r(EasyPost_Parcel::retrieve($all[$i]->id));
}
*/


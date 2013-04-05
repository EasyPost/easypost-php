<?php

require_once("../lib/Easypost.php");

EasyPost::setApiKey('Nr0PtkhoI7ogfaWswh1v7w');

/* Waiting on v2 endpoint for testing

$address_params = array("street1" => "388 Townsend St", "street2" => "Apt 20", "city" => "San Francisco", "state" => "CA", "zip" => "94107");

$address_obj = Easypost_Address::create(array('address' => $address_params));

print_r($address_obj);

//$verified_address = Easypost_Address::verify(array('address' => $address));
$verified_address = $address_obj->verify();
echo $verified_address;


*/

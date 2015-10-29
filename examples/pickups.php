<?php

require_once("../lib/easypost.php");

\EasyPost\EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

$to_address = \EasyPost\Address::create(
    array(
        "name"    => "Jeff Greenstein",
        "street1" => "2 8th St",
        "city"    => "Hermosa Beach",
        "state"   => "CA",
        "zip"     => "90254",
        "phone"   => "310-456-7890"
    )
);
$from_address = \EasyPost\Address::create(
    array(
        "company" => "EasyPost",
        "street1" => "164 Townsend",
        "street2" => "#1",
        "city"    => "San Francisco",
        "state"   => "CA",
        "zip"     => "94107",
        "phone"   => "415-379-7678"
    )
);
$parcel = \EasyPost\Parcel::create(
    array(
        "height" => 10,
        "length" => 15,
        "width" => 5,
        "weight" => 32
    )
);
$shipment = \EasyPost\Shipment::create(
    array(
        "to_address"   => $to_address,
        "from_address" => $from_address,
        "parcel"       => $parcel
    )
);
$shipment->buy($shipment->lowest_rate(array('UPS')));
$shipment->insure(array('amount' => 100));

echo $shipment->id;

$pickup = \EasyPost\Pickup::create(
    array(
        "address" => $from_address,
        "shipment"=> $shipment,
        "reference" => $shipment->id,
        "max_datetime" => date("Y-m-d H:i:s"),
        "min_datetime" => date("Y-m-d H:i:s", strtotime('+1 day')),
        "is_account_address" => false,
        "instructions" => "Will be next to garage"
    )
);

$pickup->buy(array('carrier'=>'UPS', 'service' => 'Future-day Pickup'));
echo "Confirmation: " . $pickup->confirmation . "\n";

?>

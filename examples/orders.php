<?php

require_once("../lib/easypost.php");

\EasyPost\EasyPost::setApiKey(getenv('API_KEY'));

$sf = [
    "name"    => "EasyPost.com",
    "street1" => "164 Townsend St #1",
    "street2" => "",
    "city"    => "San Francisco",
    "state"   => "CA ",
    "zip"     => "94107",
    "phone"   => "415-456-7890"
];
$maryland = [
    "name" => "Maryland",
    "street1" => "8308 Fenway Rd",
    "city" => "Bethesda",
    "state" => "MD",
    "zip" => "20817",
    "phone"   => "415-482-2937",
    "email" => "sawyer@example.com"
];

$order = \EasyPost\Order::create([
    "from_address" => $sf,
    "to_address" => $maryland,
    "shipments" => [
        [
            "parcel" => ["length" => 12.0, "width" => 10.5, "height" => 6.8, "weight" => 12],
            "options" => ["cod_amount" => 14.99]
        ],
        [
            "parcel" => ["length" => 11.9, "width" => 10.0, "height" => 7.3, "weight" => 18],
            "options" => ["cod_amount" => 9.56]
        ],
    ],
]);

echo ($order->shipments[0]->rates[0]->id . "\n");
$order->get_rates();
echo ($order->shipments[0]->rates[0]->id . "\n");

$order->buy(["carrier" => "UPS", "service" => "Ground"]);

echo ($order->id . "\n");

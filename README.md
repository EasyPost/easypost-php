Installation
------------------

Required PHP Extensions:
- [cURL](http://php.net/manual/en/book.curl.php)
- [JSON](http://php.net/manual/en/book.json.php)

Clone the EasyPost PHP client repository:

    git clone https://github.com/easypost/easypost-php

Include the EasyPost client in your PHP script:

    require_once("/path/to/easypost-php/lib/easypost.php");

Example
----------------

    <?php

    require_once("../vendor/autoload.php");
    \EasyPost\EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

    $to_address = \EasyPost\Address::create(
        array(
            "street1" => "388 Townsend St",
            "street2" => "Apt 20",
            "city"    => "San Francisco",
            "state"   => "CA",
            "zip"     => "94107"
        )
    );
    $from_address = \EasyPost\Address::create(
        array(
            "company" => "Simpler Postage Inc",
            "street1" => "764 Warehouse Ave",
            "city"    => "Kansas City",
            "state"   => "KS",
            "zip"     => "66101",
            "phone"   => "620-123-4567"
        )
    );
    $parcel = \EasyPost\Parcel::create(
        array(
            "predefined_package" => "LargeFlatRateBox",
            "weight" => 76.9
        )
    );
    $shipment = \EasyPost\Shipment::create(
        array(
            "to_address"   => $to_address,
            "from_address" => $from_address,
            "parcel"       => $parcel
        )
    );

    $shipment->buy($shipment->lowest_rate());

    echo $shipment->postage_label->label_url;


Documentation
--------------------

Up-to-date documentation at: https://www.geteasypost.com/docs/v2

Tests
--------------------
Requires [PHPUnit](https://github.com/sebastianbergmann/phpunit/)

    phpunit tests/
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

**Note: This won't work yet, only Addresses, ScanForms, and CustomsItems/CustomsInfos are implemented to date**

    EasyPost::setApiKey("Er1KtysoI4ogfaWswh1v7w");
    
    $to_param = array("street1" => "388 Townsend St", "street2" => "Apt 20", "city" => "San Francisco", "state" => "CA", "zip" => "94107");
    $from_param = array("street1" => "764 Warehouse Ave", "street2" => "", "city" => "Kansas City", "state" => "KS", "zip" => "66101");
    $parcel_param = array("predefined_package" => "LargeFlatRateBox", "weight" => 100.0); // weight in ounces

    $to = EasyPost_Address:create($to_param);
    $from = EasyPost_Address:create($from_param);
    $parcel = EasyPost_Parcel::create($parcel_param);

    $shipment = EasyPost_Shipment::create(array(
      "to" => $to,
      "from" => $from,
      "parcel" => $parcel,
    ));

    $shipment->purchase();
    echo $shipment->postage_label->label_url;

Documentation
--------------------

Up-to-date documentation at: https://www.geteasypost.com/docs

Tests
--------------------
Requires PHPUnit [https://github.com/sebastianbergmann/phpunit/]

    phpunit tests/
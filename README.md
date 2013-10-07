# EasyPost PHP Client Library

EasyPost is a simple shipping API. You can sign up for an account at https://easypost.com

Installation
------------

There are two ways to install:

 **Require Library**

```php
require_once("/path/to/lib/easypost.php");
```

**or via [Composer](http://getcomposer.org/):**

Create or add the following to composer.json in your project root:
```javascript
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/EasyPost/easypost-php"
        }
    ],
    "require": {
        "easypost/easypost-php": "dev-master"
    }
}
```

Install composer dependancies:
```shell
php composer.phar install
```

Require dependancies:
```php
require_once("/path/to/vendor/autoload.php");
```

Example
-------

```php
require_once("path/to/vendor/autoload.php");
\EasyPost\EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

$to_address = \EasyPost\Address::create(
    array(
        "name"    => "Dirk Diggler",
        "street1" => "388 Townsend St",
        "street2" => "Apt 20",
        "city"    => "San Francisco",
        "state"   => "CA",
        "zip"     => "94107",
        "phone"   => "415-456-7890"
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
```

Documentation
--------------------

Up-to-date documentation at: https://www.easypost.com/docs

Tests
--------------------

Install dev dependancies:
```shell
php composer.phar install --dev
```

Run tests:
```shell
path/to/bin/phpunit
```

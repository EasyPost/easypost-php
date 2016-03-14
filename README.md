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
        "easypost/easypost-php": "~3.0"
    }
}
```

Install composer dependencies:
```shell
php composer.phar install
```

Require dependencies:
```php
require_once("/path/to/vendor/autoload.php");
```

*NOTE:* This library relies on the [mbstring](http://php.net/manual/en/book.mbstring.php) extension. Ensure you have it [installed](http://www.php.net/manual/en/mbstring.installation.php) correctly before using the library.

Example
-------

```php
require_once("path/to/vendor/autoload.php");
\EasyPost\EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

$to_address = \EasyPost\Address::create(
    array(
        "name"    => "Dr. Steve Brule",
        "street1" => "179 N Harbor Dr",
        "city"    => "Redondo Beach",
        "state"   => "CA",
        "zip"     => "90277",
        "phone"   => "310-808-5243"
    )
);
$from_address = \EasyPost\Address::create(
    array(
        "company" => "EasyPost",
        "street1" => "118 2nd Street",
        "street2" => "4th Floor",
        "city"    => "San Francisco",
        "state"   => "CA",
        "zip"     => "94105",
        "phone"   => "415-456-7890"
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

$shipment->insure(array('amount' => 100));

echo $shipment->postage_label->label_url;
```

Documentation
--------------------

Up-to-date documentation at: https://www.easypost.com/docs

Tests
--------------------

Install dev dependencies:
```shell
php composer.phar install --dev
```

Run tests:
```shell
path/to/bin/phpunit
```

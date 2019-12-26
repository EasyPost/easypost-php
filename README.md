# EasyPost PHP Client Library

[![Build Status](https://travis-ci.org/EasyPost/easypost-php.svg?branch=master)](https://travis-ci.org/EasyPost/easypost-php)

EasyPost is a simple shipping API. You can sign up for an account at https://easypost.com.

## Installation

*NOTE: Although this library has been tested with PHP 5.3^, it's highly recommended to use PHP 7.2^. Additionally, This library relies on the [mbstring](http://php.net/manual/en/book.mbstring.php) extension. Ensure you have it [installed](http://www.php.net/manual/en/mbstring.installation.php) correctly before using the library.*

### Install Client Library

**Recommended Installation: [Composer](http://getcomposer.org/)**

```shell
composer require easypost/easypost-php
```

**OR: Manually Require Library**

Manually download and drop the library in your project.

```php
require_once("/path/to/lib/easypost.php");
```

### Install Dependencies

**Recommended Dependency Installation:**
```shell
composer install

OR

php composer.phar install
```

**OR: Manually Require Dependencies:**
```php
require_once("/path/to/vendor/autoload.php");
```

## Example

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

## Documentation

Up-to-date documentation can be found at: https://www.easypost.com/docs.

## Testing & Development

***NOTE: Unit tests only work with PHP 7.2^.***

Ensure dependencies are installed, then run any of the following:

**Fix PHP Standards:**
```shell
./bin/php-cs-fixer fix mydir --verbose --show-progress=estimating     
```

**Run Linting:**
```shell
./bin/phplint ./dir
```

**Run Unit Tests:**
```shell
./bin/phpunit
```

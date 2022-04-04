# EasyPost PHP Client Library

[![CI](https://github.com/EasyPost/easypost-php/workflows/CI/badge.svg)](https://github.com/EasyPost/easypost-php/actions?query=workflow%3ACI)

EasyPost, the simple shipping solution. You can sign up for an account at https://easypost.com.

## Install

**NOTE:** This library relies on the [mbstring](http://php.net/manual/en/book.mbstring.php) extension. Ensure you have it [installed](http://www.php.net/manual/en/mbstring.installation.php) correctly before using the library.

```bash
# Install via Composer
composer require easypost/easypost-php
```

```php
# Require the autoloader:
require_once("/path/to/vendor/easypost/autoload.php");

# Alternatively, manually download and require:
require_once("/path/to/lib/easypost.php");
```

## Usage

A simple create & buy shipment example:

```php
require_once("/path/to/vendor/easypost/autoload.php");

\EasyPost\EasyPost::setApiKey(getenv('EASYPOST_API_KEY'));

$shipment = \EasyPost\Shipment::create([
    "from_address" => [
        "company" => "EasyPost",
        "street1" => "118 2nd Street",
        "street2" => "4th Floor",
        "city"    => "San Francisco",
        "state"   => "CA",
        "zip"     => "94105",
        "phone"   => "415-456-7890",
    ],
    "to_address" => [
        "name"    => "Dr. Steve Brule",
        "street1" => "179 N Harbor Dr",
        "city"    => "Redondo Beach",
        "state"   => "CA",
        "zip"     => "90277",
        "phone"   => "310-808-5243",
    ],
    "parcel" => [
        "length" => 20.2,
        "width"  => 10.9,
        "height" => 5,
        "weight" => 65.9,
    ],
]);

$shipment->buy($shipment->lowest_rate());

echo $shipment;
```

## Documentation

API Documentation can be found at: https://easypost.com/docs/api.

## Development

**NOTE:** Recording VCR cassettes only works with PHP 7.3 or 7.4. Once recorded, tests can be run on PHP 7 or 8.

```bash
# Install dependencies
composer install

# Lint project
composer lint

# Fix linting errors
composer fix

# Run tests
EASYPOST_TEST_API_KEY=123... EASYPOST_PROD_API_KEY=123... composer test

# Generate coverage reports (requires Xdebug)
EASYPOST_TEST_API_KEY=123... EASYPOST_PROD_API_KEY=123... composer coverage
```

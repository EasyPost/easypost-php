# EasyPost PHP Client Library

[![CI](https://github.com/EasyPost/easypost-php/workflows/CI/badge.svg)](https://github.com/EasyPost/easypost-php/actions?query=workflow%3ACI)
[![PHP version](https://badge.fury.io/ph/easypost%2Feasypost-php.svg)](https://badge.fury.io/ph/easypost%2Feasypost-php)

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

Upgrading major versions of this project? Refer to the [Upgrade Guide](UPGRADE_GUIDE.md).

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

### Testing

The test suite in this project was specifically built to produce consistent results on every run, regardless of when they run or who is running them. This project uses [VCR](https://github.com/php-vcr/php-vcr) to record and replay HTTP requests and responses via "cassettes". When the suite is run, the HTTP requests and responses for each test function will be saved to a cassette if they do not exist already and replayed from this saved file if they do, which saves the need to make live API calls on every test run.

If you make an addition to this project, the request/response will get recorded automatically for you when `VCR::insertCassette('object/action.yml');` is added to the beginning of a test function. When making changes to this project, you'll need to re-record the associated cassette to force a new live API call for that test which will then record the request/response used on the next run.

The test suite has been populated with various helpful fixtures that are available for use, each completely independent from a particular user **with the exception of the USPS carrier account ID** which has a fallback value to our internal testing user's ID. If you are a non-EasyPost employee and are re-recording cassettes, you may need to provide the `USPS_CARRIER_ACCOUNT_ID` environment variable with the ID associated with your USPS account (which will be associated with your API keys in use) for tests that use this fixture.

**Note on dates:** Some fixtures use hard-coded dates that may need to be incremented if cassettes get re-recorded (such as reports or pickups).

**Note on test order:** This project uses a special test ordering which follows a `create -> retrieve -> update -> delete` pattern. The create test will return the created object so that other tests such as retrieving, updating, and deleting tests can reuse that object for their own tests in an effort to cut down on object creation during testing. You'll see this denoted with a `@depends` decorator on tests where you can link the test order as needed.

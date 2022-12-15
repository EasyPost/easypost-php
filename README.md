# EasyPost PHP Client Library

[![CI](https://github.com/EasyPost/easypost-php/workflows/CI/badge.svg)](https://github.com/EasyPost/easypost-php/actions?query=workflow%3ACI)
[![Coverage Status](https://coveralls.io/repos/github/EasyPost/easypost-php/badge.svg?branch=master)](https://coveralls.io/github/EasyPost/easypost-php?branch=master)
[![PHP version](https://badge.fury.io/ph/easypost%2Feasypost-php.svg)](https://badge.fury.io/ph/easypost%2Feasypost-php)

EasyPost, the simple shipping solution. You can sign up for an account at <https://easypost.com>.

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

$client = new EasyPostClient(getenv('EASYPOST_API_KEY'));

$shipment = $client->shipmemt->create([
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

$boughtShipment = $client->shipment->buy($shipment->id, $shipment->lowestRate());

echo $boughtShipment;
```

## Documentation

API Documentation can be found at: <https://easypost.com/docs/api>.

Upgrading major versions of this project? Refer to the [Upgrade Guide](UPGRADE_GUIDE.md).

## Development

**NOTE:** Recording VCR cassettes only works with PHP 7.4. Once recorded, tests can be run on PHP 7.4 or 8.0+.

```bash
# Install dependencies
make install

# Update dependencies
make update

# Lint project
make lint

# Fix linting errors
make fix

# Run tests
EASYPOST_TEST_API_KEY=123... EASYPOST_PROD_API_KEY=123... make test

# Generate coverage reports (requires Xdebug for HTML report)
EASYPOST_TEST_API_KEY=123... EASYPOST_PROD_API_KEY=123... make coverage

# Run security analysis
make scan

# Generate library documentation (requires phpDocumentor.phar in the root of the project)
make docs

# Update submodules
git submodule init
git submodule update --remote
```

### Testing

The test suite in this project was specifically built to produce consistent results on every run, regardless of when they run or who is running them. This project uses [VCR](https://github.com/php-vcr/php-vcr) to record and replay HTTP requests and responses via "cassettes". When the suite is run, the HTTP requests and responses for each test function will be saved to a cassette if they do not exist already and replayed from this saved file if they do, which saves the need to make live API calls on every test run. If you receive errors about a cassette expiring, delete and re-record the cassette to ensure the data is up-to-date.

**Sensitive Data:** We've made every attempt to include scrubbers for sensitive data when recording cassettes so that PII or sensitive info does not persist in version control; however, please ensure when recording or re-recording cassettes that prior to committing your changes, no PII or sensitive information gets persisted by inspecting the cassette.

**Making Changes:** If you make an addition to this project, the request/response will get recorded automatically for you when `TestUtil::setupCassette('object/action.yml');` is added to the beginning of a test function. When making changes to this project, you'll need to re-record the associated cassette to force a new live API call for that test which will then record the request/response used on the next run.

**Test Data:** The test suite has been populated with various helpful fixtures that are available for use, each completely independent from a particular user **with the exception of the USPS carrier account ID** (see [Unit Test API Keys](#unit-test-api-keys) for more information) which has a fallback value of our internal testing user's ID. Some fixtures use hard-coded dates that may need to be incremented if cassettes get re-recorded (such as reports or pickups).

#### Unit Test API Keys

The following are required on every test run:

- `EASYPOST_TEST_API_KEY`
- `EASYPOST_PROD_API_KEY`

Some tests may require an EasyPost user with a particular set of enabled features such as a `Partner` user when creating referrals. We have attempted to call out these functions in their respective docstrings. The following are required when you need to re-record cassettes for applicable tests:

- `USPS_CARRIER_ACCOUNT_ID` (eg: one-call buying a shipment for non-EasyPost employees)
- `PARTNER_USER_PROD_API_KEY` (eg: creating a referral user)
- `REFERRAL_USER_PROD_API_KEY` (eg: adding a credit card to a referral user)

#### Mocking

Some of our unit tests require HTTP calls that cannot be easily tested with live/recorded calls (e.g. HTTP calls that trigger payments or interact with external APIs).

We have implemented a custom, lightweight HTTP mocking functionality in this library that allows us to mock HTTP calls and responses.

A mock client is the same as a normal client, with a set of mock request-response pairs stored as a property.

At the time of making a real HTTP request, a mock client will instead check which mock request entry matches the queued request (matching by HTTP method and a regex pattern for the URL), and will return the corresponding mock response (HTTP status code and body).

**NOTE**: If a client is configured with a mocking utility, it will ONLY make mock requests. If it attempts to make a request that does not match any of the configured mock requests, the request will fail and trigger an exception.

To use the mocking utility:

```php
use EasyPost\Test\mocking\MockingUtility;
use EasyPost\Test\mocking\MockRequest;
use EasyPost\Test\mocking\MockRequestMatchRule;
use EasyPost\Test\mocking\MockRequestResponseInfo;

// create a mocking utility with a list of mock request-response pairs
$mockingUtility = new MockingUtility(
    new MockRequest(
        new MockRequestMatchRule(
            // HTTP method and regex pattern for the URL must both pass for the request to match
            'post',
            '/v2\\/bank_accounts\\/\\S*\\/charges$/'
        ),
        new MockRequestResponseInfo(
            // HTTP status code and body to return when this request is matched
            200,
            '{}'
        )
    ),
    ... // more mock requests
);

// create a new client with the mocking utility
$client = new EasyPostClient("some_key", Constants::TIMEOUT, Constants::API_BASE, $mockUtility);

// use the client as normal
```
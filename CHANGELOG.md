# CHANGELOG

## v5.6.0 (2022-08-25)

- Moves EndShipper out of beta into the general library namespace

## v5.5.1 (2022-08-20)

- Removes some extra imports that no longer exist causing errors when importing this library. Adds a regression test to protect against this in the future.

## v5.5.0 (2022-08-02)

- Adds Carbon Offset support
  - Adds ability to create a shipment with carbon offset
  - Adds ability to buy a shipment with carbon offset
  - Adds ability to one-call-buy a shipment with carbon offset
  - Adds ability to rerate a shipment with carbon offset
- Adds `validateWebhook` function that returns your webhook or raises an error if there is a `webhookSecret` mismatch
- Fixes a bug that required the values of `verify` and `verify_strict` params on an Address creation call to be arrays since passing `true` as a boolean is also valid (passing an array will continue to work but is no longer required)

## v5.4.0 (2022-07-18)

- Add ability to generate forms for shipments via `generate_form()` function

## v5.3.0 (2022-07-07)

- Adds `Billing::retrieve_payment_methods()`, `Billing::fund_wallet()`, and `Billing::delete_payment_method()` functions
- Adds OS specific details to the `User-Agent` header
- Applies a patch to coerce array error messages to strings where error mapping is done improperly (as is the case with carriers like GSO/GLS) (closes #181)

## v5.2.1 (2022-05-31)

- Fixes a missing parameter bug by setting the `beta` parameter in the `refresh` function to `false` by default

## v5.2.0 (2022-05-19)

- Adds the `EndShipper` Beta class with `create`, `retrieve`, `all`, and `save` functions

## v5.1.1 (2022-05-09)

- Adds present but unimported supplemental EasyPost object classes such as `TrackingDetails` to the lib (closes #164)

## v5.1.0 (2022-05-09)

- Adds a `lowest_rate()` function to Orders and Pickups
- Adds `Shipment::get_lowest_smartrate()` and `$shipment.lowest_smartrate()` functions
- Removes the `params` from the `Address->verify()` method since it's non-static and unused
- Removes dead conditional `message` check in `Address::create_and_verify`

## v5.0.0 (2022-02-09)

Upgrading major versions of this project? Refer to the [Upgrade Guide](UPGRADE_GUIDE.md).

- Bumped minimum PHP version supported from `5.3` to `7.3`
- Adds a full test suite, all functions are now tested for each object
- Adds class properties to every class (closes #96)
- Updates and adds docblocks to each function throughout the library
- Switched from `array()` syntax to `[]` throughout the project
- Adds the `update_brand()` method on the `User` object
- Set a default timeout of 30 seconds for connections and 60 seconds for requests
- Fixes the `create_list()` Tracker and `create_and_buy()` Batch method's encodings to properly send through the parameters
- Adds the PHP version used to the `User-Agent` header
- Removed unusable `all()` method on the `Parcel` object
- Removed unpaginated `all()` method on the `Order`, `CustomsInfo`, and `CustomsItem` objects
- Removed the `get_rates()` method on the Shipment object since a Shipment object already has rates. If you need new rates for a shipment, use the `regenerate_rates()` method instead
- Ignores return types for now on `ArrayAccess` and `Iterator` methods (closes #124)
- Bumps dev dependencies
- Various other small improvements and bug fixes

## v4.0.2 (2021-10-20)

- Further fixes JSON encoding by dropping null key/values, sending values as strings where necessary (returning to previous behavior), and removing the `array_filter` from the previous release

## v4.0.1 (2021-10-08)

- Properly handles empty lists and objects with JSON encoding (closes #114)

## v4.0.0 (2021-10-06)

Upgrading major versions of this project? Refer to the [Upgrade Guide](UPGRADE_GUIDE.md).

- JSON encodes POST bodies instead of form encoding them
- Adds TaxIdentifier support
- Corrects references of `contact@easypost.com` to `support@easypost.com`
- Bumps patch versions of dependencies

## v3.6.0 (2021-06-10)

- Adds `SmartRate` functionality to the `Shipments` object (available by calling `get_smartrates()` on a shipment)

## v3.5.0 (2020-08-09)

- Added event retrieval methods
- Fixed a bug that did not allow carrier accounts to be deleted
- Fixed a bug that did not allow users to be deleted
- Removed various pieces of old code

## v3.4.5 (2020-05-07)

- Added "Refund" and "Shipment Invoice" to the prefix and type lists

## v3.4.4 (2020-03-11)

- Version bump with updated author

## v3.4.3 (2020-03-10)

- Bumped version in package.json for Packagist
- Updated README with release instructions

## v3.4.2 (2020-03-09)

- Fixed a bug that would not create reports properly
- Added unit tests for reports

## v3.4.1 (2018-04-13)

- Added ability to set connection and response timeouts for all EasyPost actions (thanks mattjanssen!)

## v3.4.0 (2017-11-30)

- Renamed `Object` class `EasyPostObject` since Object is a keyword in PHP7.2

## v3.3.4 (2017-05-24)

- Fixed confusing Pickup example
- Enabled Reports to retrieve without a type parameter
- Use X-Client-User-Agent over X-EasyPost-Client-User-Agent

## v3.3.3 (2017-02-14)

- Added get_rates method to Order objects

## v3.3.2 (2017-01-24)

- Fixed compatibility issues with older PHP versions and the Report class

## v3.3.1 (2017-01-19)

- Fixed Create for ScanForms

## v3.3.0 (2017-01-17)

- Added basic CRUD methods for Webhooks

## v3.2.1 (2016-12-14)

- Fixed error in Util.php they may have affected shipment_reports retrievals

## v3.2.0 (2016-12-12)

- Added ability to create Report objects for shipment, tracker and payment_log reports

## v3.1.3 (2016-10-25)

- Fixed warning message from Address Verify/VerifyStrict

## v3.1.2 (2016-09-16)

- Added create_list for trackers

## v3.1.1 (2016-07-21)

- Added Insurance support

## v3.1.0 (2016-06-02)

- Renamed "Resource" class to "EasypostResource" since Resource is reserved in PHP7 (thanks ccovey!)

## v3.0.3 (2016-02-22)

- Added ability to create top-level users

## v3.0.2 (2016-01-29)

- Added 'verify' and 'verify_strict' options to Address creation

## v3.0.1 (2016-01-05)

- Updated ReadMe (thanks browner12!)
- Added DocBlock annotations to all methods (thanks browner12!)

## v3.0.0 (2015-12-18)

- Fixed Shipment Retrieve by tracking_code

## v2.1.2 (2015-11-04)

- Added Tracker.all example, and updated some old examples

## v2.1.1 (2015-07-01)

- Removed cURL options for SSL version, timeout, and connect timeout. The
  default values for these options are acceptable.

## v2.1.0 (2015-04-10)

- Added User and CarrierAccount resources and examples for each.
- Modified EasyPost Object setter to record the change in the top level parent
  object rather than the object that the change was made in. This allows you to
  change sub objects and then call save() on the parent to have all properties
  saved.
- Removed EasyPost Object transientValues and made unsavedValues act as a
  representation of the params to send on save, rather than a flag to indicate
  if a property had been changed.
- Added support for EasyPost's new error format: {"error": {"message": "...",
  "code": ..., "errors": {...}}} as well as a prettyPrint function to better
  display errors in development.
- Added support for additional HTTP methods PATCH and PUT.
- EasyPost Objects are now Iterable.

## v2.0.12 (2015-02-04)

- Added pickup class
- Added pickup example

## v2.0.11 (2014-11-04)

- Added tracker to shipment buy response
- Updated tracker examples

## v2.0.10 (2014-07-07)

- Added Item, Container, and Order resources.

## v2.0.9 (2014-04-08)

- Updated the CA cert bundle.

## v2.0.8 (2013-12-29)

- Added the ability to add negative filters to Shipment->lowest_rate by starting them with an exclamation mark.

## v2.0.7 (2013-11-08)

- Added endpoint to create shipment from tracking code.

## v2.0.6 (2013-10-21)

- Added Event resource for webhooks.

## v2.0.5 (2013-08-30)

- API Addition: Batch.buy method added. Trigger the purchasing of labels after a successful Batch.create.
- API Addition: Batch.scan_form method added. Quickly generate a scan_form for an entire batch.

## v2.0.4 (2013-07-31)

- API Addition: Tracker objects added. Trackers can be used to register any tracking code with EasyPost webhooks.

## v2.0.3 (2013-07-16)

- Bug fix: error messages should no longer be truncated to their first character.

## v2.0.2 (2013-07-05)

- Added function to Address to all creating and verifying at the same time.

## v2.0.1 (2013-07-01)

- Add label function to Shipment to request specific label file_formats (pdf, epl2, zpl).
- Add insure function to Shipment. Add insurance to any shipment in one call!

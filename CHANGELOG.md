# CHANGELOG

## v6.9.1 (2023-11-20)

- Fixes a bug that globally reset the timezone to UTC instead of setting the timezone per-request (closes #310)

## v6.9.0 (2023-10-11)

- Deprecates API key-related functions in the `user` service and introduces the replacement functions in the `api_keys` service
- Fixes a bug that changed the timezone to UTC on each request without resetting it to the original timezone in use (closes #310)
- Fixes documentation generation command invocation

## v6.8.1 (2023-09-05)

- Fix endpoint for creating a FedEx Smartpost carrier account

## v6.8.0 (2023-07-28)

- Adds new `RequestHook` and `ResponseHook` events. (un)subscribe to them with the new `subscribeToRequestHook`, `subscribeToResponseHook`, `unsubscribeFromRequestHook`, or `unsubscribeFromResponseHook` methods of an `EasyPostClient`
- Maps 400 status codes to new `BadRequestException` class

## v6.7.0 (2023-06-06)

- Migrates carrier metadata to GA (now available via `client.carrierMetadata.retrieve`)

## v6.6.0 (2023-05-02)

- Adds `retrieveEstimatedDeliveryDate` function to the Shipment class

## v6.5.0 (2023-04-18)

- Adds beta `retrieveCarrierMetadata` function
- Improves Error Deserialization to dynamically handle edge cases that have a bad format

## v6.4.0 (2023-04-04)

- Add `getNextPage` function that retrieves the next page of results for a paginated collection

## v6.3.1 (2023-03-22)

- Adds missing `status_detail` property to Tracker
- Fixes spelling of `weight` property on Parcel

## v6.3.0 (2023-02-17)

- Adds beta `retrieveStatelessRates` function
- Adds `getLowestStatelessRate` utility
- Correct HTTP 403 status responses to throw `ForbiddenException`
- Various other small corrections and improvements

## v6.2.0 (2023-01-18)

- Added function to retrieve all pickups via `$client->pickup->all()`
- Added payload functions `retrievePayload` and `retrieveAllPayloads` methods, accessible via `$client->event` service.

## v6.1.0 (2023-01-11)

- Adds new beta billing functionality for referral customer users, accessible via the `$client->betaReferralCustomer` service
  - `addPaymentMethod` to add an existing Stripe bank account or credit card to your EasyPost account
  - `refundByAmount` refunds you wallet balance by a specified amount
  - `refundByPaymentLog` refunds you wallet balance by a specified payment log

## v6.0.0 (2023-01-05)

- Release final version of v6 that contains all the changes in the `v6.0.0-rc1` below

## v6.0.0-rc1 (2022-12-15)

- PHP 7.3 is no longer supported
- Added a new `EasyPostClient` object which encapsulates the API key. All functions are called against this client allowing for thread-safety (eg: `$client->shipment->create()`)
  - All instance methods and the ability to have objects update in-place has been removed due to this rearchitecture. When updating objects or performing actions on them (eg: creating a label/scanform), you will need to assign the return value to a variable and use that moving forward instead of relying on the object getting updated without capturing the new return value
  - `->save()` instance methods are now `update()` static methods
  - Functions no longer accept an API key as an optional parameter
  - EasyPost objects no longer contain the logic associated with them; instead, we have `Services` for each EasyPost object. All the services are properties of an `EasyPostClient`. You can then call functions on a Service.
- All function and parameter names are now camelCase. Previously we used a mix of camel and snake cases
- Improves error exception handling (closes #7)
  - Introduced ~2 dozen new exception types that extend from either `ApiException` or `EasyPostException`
  - ApiExceptions will behave like the previous EasyPostException class did. They will include a `message`, `errors`, `code`, `httpStatus` and `httpBody`. This class extends the more generic EasyPostException which only contains a message, used for exceptions thrown directly from this library
  - The `ecode` property of an `ApiException` is now just `code`
- Functions that previously returned `true` now return void as there is no response body from the API (eg: `fundWallet`, `deletePaymentMethod`, `updateEmail`, `createList`)
- Adopts `Guzzle` as the HTTP client for this library. This should provide a much more consistent experience, better encoding, and faster request times in some cases
- The results of calling `allApiKeys` is no longer double wrapped with the mode of the API key (these are still accessible inside of each object)
- `Requestor` has moved to `Http`, constants from `EasyPost` now live in `Constants`, `Error` moved to `Exception`
- Occurances of `smartrate` are now `smartRate` and `Smartrate` are now `SmartRate` to match the documentation and API expectations
- `Referral` is now `ReferralCustomer` to better match documentation and API expectation
- `validateWebhook`, `getLowestSmartRate`, and `receiveEvent` are now under `EasyPost\Util\Util` as they do not make any API calls and do not need the associated client object
  - The `receive` function previously in the namespace of `Event` is now called `receiveEvent` since it has been relocated to the generic Util namespace
  - Internal, library only utilities have been moved to `EasyPost\Util\InternalUtil`
- The beta `EndShipper` class has been removed, please use the generally available `EndShipper` class
- Various properties and functions that were previously intended for private/protected use but were public have been corrected
- All phpdoc type hints, descriptions, return values, and throws references were corrected or updated
- All dependencies were bumped
- Various other bug fixes and improvements were made along with addressing deprecation warnings

## v5.8.0 (2022-12-01)

- Adds carrier account creation routes requests correctly for carriers that require custom workflows
- Instead of returning `null` when a list of child API keys cannot be returned (eg: when you call `api_keys` on a non-user object) it will return an empty list. The expected impact to end users is extremely low
- Retrieving child user API keys for users with large numbers of child users should see much faster results as we now break on the match instead of iterating the entire list

## v5.7.0 (2022-09-21)

- Adds Partner White Label (Referral) Support
  - Adds ability to create a referral user
  - Adds ability to update a referral user's email
  - Adds ability to retrieve all referral users
  - Adds ability to add a credit card to a referral user's account
- Adds support to specify an `$endShipperId` on the buy call of a Shipment
- Removes unreachable code in the address verification flow that checked for the existence of an address (errors will continue to be thrown on failure)

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

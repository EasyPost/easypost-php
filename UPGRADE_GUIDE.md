# Upgrade Guide

Use the following guide to assist in the upgrade process of the `easypost-php` library between major versions.

- [Upgrading from 7.x to 8.0](#upgrading-from-7x-to-80)
- [Upgrading from 6.x to 7.0](#upgrading-from-6x-to-70)
- [Upgrading from 5.x to 6.0](#upgrading-from-5x-to-60)
- [Upgrading from 4.x to 5.0](#upgrading-from-4x-to-50)
- [Upgrading from 3.x to 4.0](#upgrading-from-3x-to-40)

## Upgrading from 7.x to 8.0

### 8.0 High Impact Changes

- [Drop Support for PHP 8.0](#80-drop-support-for-php-80)
- [Error Parsing](#80-error-parsing)

### 8.0 Medium Impact Changes

- [Deprecations](#80-deprecations)

### 8.0 Drop Support for PHP 8.0

*Likelihood of Impact: **High***

This library now requires PHP 8.1 or greater.

## 8.0 Error Parsing

*Likelihood of Impact: **High***

The `errors` key of an error response can return either a list of `FieldError` objects or a list of strings. The error parsing has been expanded to include both formats. As such, you will now need to check for the format of the `errors` field and handle the errors appropriately for the type that is returned.

## 8.0 Deprecations

*Likelihood of Impact: **Medium***

The following deprecated functions have been removed:

- `tracker.createList` (trackers must be created individually moving forward)
- `user.allApiKeys` (use `apiKey.all`)
- `user.apiKeys` (use `apiKey.retrieveApiKeysForUser`)

## Upgrading from 6.x to 7.0

**NOTICE:** v7 is deprecated.

### 7.0 High Impact Changes

- [Drop Support for PHP 7.4](#70-drop-support-for-php-74)
- [Type Declarations Added](#70-type-declarations-added)
- [Carbon Offset Removed](#70-carbon-offset-removed)

### 7.0 Low Impact Changes

- [createAndBuy Batch Function Removed](#70-createandbuy-batch-function-removed)
- [updateEmail Parameter Order Corrected](#70-updateemail-parameter-order-corrected)

### 7.0 Drop Support for PHP 7.4

With the introduction of PHP 8.3, support for PHP 7.4 has been dropped. This library now requires PHP 8.0 or greater.

### 7.0 Type Declarations Added

The library previously had type hints available via docstrings. With PHP 8.0+ support, the library now has type declarations for every function parameter and return value. You will want to double check that the types passed to each function match the expectations to avoid `TypeError`s being thrown at runtime. Docstrings have been updated to match and assist IDEs in this process. You may notice some changes in what was expected before and the corrected types now. We've made every attempt to retain where possible the previous values or loose possible options to allow for the maximum backwards compatibility.

### 7.0 Carbon Offset Removed

EasyPost now offers Carbon Neutral shipments by default for free! Because of this, there is no longer a need to specify if you want to offset the carbon footprint of a shipment. The `withCarbonOffset` parameter of the `create`, `buy`, and `regenerateRates` shipment functions have been removed as a result. This is a high-impact change for those using `EndShippers` as the function interfaces have changed. You will need to inspect the callsites to create and buy shipments to ensure that the EndShipper parameter is being passed in the correct place now that the `withCarbonOffset` parameter has been removed.

### 7.0 createAndBuy Batch Function Removed

The `create_and_buy` Batch endpoint has been deprecated and has thus been removed from the library. The proper flow is to create a batch first and buy it separately with two API calls.

### 7.0 updateEmail Parameter Order Corrected

The `updateEmail` function of the `ReferralCustomer` service mistakenly had the `email` parameter first when it should have had `userId` first. This change has been made to ensure that every function that accepts an ID always has the ID come first. If you use this function, you will simply need to swap the order of the two parameters.

## Upgrading from 5.x to 6.0

**NOTICE:** v6 is deprecated.

### 6.0 High Impact Changes

- [Drop Support for PHP 7.3](#60-drop-support-for-php-73)
- [New EasyPostClient Object](#60-new-easypostclient-object)
- [Corrected Naming Conventions](#60-corrected-naming-conventions)
- [Improved Exceptions](#60-improved-exceptions)

### 6.0 Medium Impact Changes

- [Changed Function Return Types](#60-changed-function-return-types)
- [Moved Functions](#60-moved-functions)

### 6.0 Drop Support for PHP 7.3

With the introduction of PHP 8.2, support for PHP 7.3 has been dropped. This library now requires PHP 7.4 or greater.

### 6.0 New EasyPostClient Object

This library is now thread-safe with the introduction of a new `EasyPostClient` object. Instead of defining a global API key that all requests use, you create an `EasyPostClient` object and pass your API key to it. You then call your desired functions against a "service" which coincide with EasyPost objects:

```php
// Old method
\EasyPost\EasyPost::setApiKey($_ENV['EASYPOST_API_KEY']);
$shipment = \EasyPost\Shipment::create(['data' => 'here']);

// New method
$client = new EasyPostClient(getenv('EASYPOST_API_KEY'));
$shipment = $client->shipment->create(['data' => 'here']);
```

All instance methods are now static with the exception of `lowestRate` as these make API calls and require the client object (EasyPost objects do not contain an API key to make API calls with).

Previously used `->save()` instance methods are now static `update()` functions where you specify first the ID of the object you are updating and second, the parameters that need updating.

Functions no longer accept an API key as an optional parameter. If you need per-function API key changes, create a new Client object and call the function on the new client that uses the API key you need.

### 6.0 Corrected Naming Conventions

We previously had a mix of camelCase and snake_case function and parameter names in this library. These have all been corrected to only use `camelCase`. Things like `lowestRate`, `allApiKeys`, etc have changed.

The `Referral` class is now called `ReferralCustomer` to match our documentation and API.

Occurances of `smartrate` are now `smartRate` and `Smartrate` are now `SmartRate` to match the documentation and API expectations.

### 6.0 Improved Exceptions

Introduced ~2 dozen new exception types that extend from either `ApiException` or `EasyPostException`.

New ApiExceptions (extends EasyPostException):

- `EasyPost/Exception/Api/ApiException`
- `EasyPost/Exception/Api/EncodingException`
- `EasyPost/Exception/Api/ExternalApiException`
- `EasyPost/Exception/Api/ForbiddenException`
- `EasyPost/Exception/Api/GatewayTimeoutException`
- `EasyPost/Exception/Api/HttpException`
- `EasyPost/Exception/Api/InternalServerException`
- `EasyPost/Exception/Api/InvalidRequestException`
- `EasyPost/Exception/Api/JsonException`
- `EasyPost/Exception/Api/MethodNotAllowedException`
- `EasyPost/Exception/Api/NotFoundException`
- `EasyPost/Exception/Api/PaymentException`
- `EasyPost/Exception/Api/RateLimitException`
- `EasyPost/Exception/Api/RedirectException`
- `EasyPost/Exception/Api/ServiceUnavailableException`
- `EasyPost/Exception/Api/TimeoutException`
- `EasyPost/Exception/Api/UnauthorizedException`
- `EasyPost/Exception/Api/UnknownApiException`

New EasyPostExceptions (extends \Exception):

- `/EasyPost/Exception/General/FilteringException`
- `/EasyPost/Exception/General/InvalidObjectException`
- `/EasyPost/Exception/General/InvalidParameterException`
- `/EasyPost/Exception/General/MissingParameterException`
- `/EasyPost/Exception/General/SignatureVerificationException`

ApiExceptions will behave like the previous EasyPostException class did. They will include a `message`, `errors`, `code`, `httpStatus` and `httpBody`. This class extends the more generic EasyPostException which only contains a message, used for exceptions thrown directly from this library

The `ecode` property of an `ApiException` is now just `code`

### 6.0 Changed Function Return Types

Functions that previously returned `true` now return void as there is no response body from the API (eg: `fundWallet`, `deletePaymentMethod`, `updateEmail`, `createList`)

The results of calling `allApiKeys` is no longer double wrapped with the mode of the API key (these are still accessible inside of each object)

### 6.0 Moved Functions

The `validateWebhook`, `getLowestSmartRate`, and `receive` functions are now under `EasyPost\Util\Util` as they do not make any API calls and do not need the associated client object.

The `receive` function previously in the namespace of `Event` is now called `receiveEvent` since it has been relocated to the generic Util namespace.

## Upgrading from 4.x to 5.0

**NOTICE:** v5 is deprecated.

[v5 Docs](https://github.com/EasyPost/examples/tree/master/official/docs/php/v5)

### 5.0 High Impact Changes

- [Updating Dependencies](#50-updating-dependencies)

### 5.0 Medium Impact Changes

- [Default Timeouts for HTTP Requests](#50-default-timeouts-for-http-requests)
- [Removal of `all` method from the `Order`, `CustomsInfo`, and `CustomsItem` Objects](#50-removal-of-all-method-from-the-order-customsinfo-and-customsitem-objects)
- [Removal of `get_rates` Shipment Method](#50-removal-of-get_rates-shipment-method)

### 5.0 Low Impact Changes

- [Removal of all Method from the Parcel Object](#50-removal-of-all-method-from-the-parcel-object)

### 5.0 Updating Dependencies

Likelihood of Impact: High

**PHP 7.3 Required**

easypost-php now requires PHP 7.3 or greater.

**Dependencies**

All dependencies had minor version bumps.

### 5.0 Default Timeouts for HTTP Requests

Likelihood of Impact: Medium

Default timeouts for all HTTP requests are now set to 30 seconds for connection and 60 seconds for requests. If you require longer timeouts, you can set them by overriding the defaults:

```php
// Timeouts are in milliseconds
EasyPost::setConnectTimeout(30000);
EasyPost::setResponseTimeout(60000);
```

### 5.0 Removal of all() Method from the Order, CustomsInfo, and CustomsItem Objects

Likelihood of Impact: Medium

The `/all` endpoint for the Order, CustomsInfo, and CustomsItem objects are not paginated and have therefore been removed from the library.

### 5.0 Removal of get_rates() Shipment Method

Likelihood of Impact: Medium

The HTTP method used for the `get_rates` endpoint at the API level has changed from `POST` to `GET` and will only retrieve rates for a shipment instead of regenerating them. A new `/rerate` endpoint has been introduced to replace this functionality; In this library, you can now call the `Shipment::regenerate_rates` method to regenerate rates. Due to the logic change, the `get_rates` method has been removed since a Shipment inherently already has rates associated.

### 5.0 Removal of all() Method from the Parcel Object

Likelihood of Impact: Low

There is no `/all` endpoint for the Parcel object. This function was removed as it was unusable.

## Upgrading from 3.x to 4.0

**NOTICE:** v4 is deprecated.

### 4.0 High Impact Changes

- [JSON Encoded Bodies](#40-json-encoded-bodies)

### 4.0 Low Impact Changes

- [Updating Dependencies](#40-updating-dependencies)

### 4.0 JSON Encoded Bodies

Likelihood of Impact: High

All `POST` and `PUT` request bodies are now JSON encoded instead of form-encoded. You may see subtle inconsistencies to how certain data types were previously sent to the API. We have taken steps to mitigate and test against these edge cases.

### 4.0 Updating Dependencies

Likelihood of Impact: Low

**Dependencies**

All dependencies had their patch versions bumped.

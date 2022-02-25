# Upgrade Guide

Use the following guide to assist in the upgrade process of the `easypost-php` library between major versions.

* [Upgrading from 4.x to 5.0](#upgrading-from-4x-to-50)
* [Upgrading from 3.x to 4.0](#upgrading-from-3x-to-40)

## Upgrading from 4.x to 5.0

### 5.0 High Impact Changes

* [Updating Dependencies](#50-updating-dependencies)

### 5.0 Medium Impact Changes

* [Default Timeouts for HTTP Requests](#50-default-timeouts-for-http-requests)
* [Removal of `all` method from the `Order`, `CustomsInfo`, and `CustomsItem` Objects](#50-removal-of-all-method-from-the-order-customsinfo-and-customsitem-objects)
* [Removal of `get_rates` Shipment Method](#50-removal-of-getrates-shipment-method)

### 5.0 Low Impact Changes

* [Removal of all Method from the Parcel Object](#50-removal-of-all-method-from-the-parcel-object)

## 5.0 Updating Dependencies

Likelihood of Impact: High

**PHP 7.3 Required**

easypost-php now requires PHP 7.3 or greater.

**Dependencies**

All dependencies had minor version bumps.

## 5.0 Default Timeouts for HTTP Requests

Likelihood of Impact: Medium

Default timeouts for all HTTP requests are now set to 30 seconds for connection and 60 seconds for requests. If you require longer timeouts, you can set them by overriding the defaults:

```php
// Timeouts are in milliseconds
EasyPost::setConnectTimeout(30000);
EasyPost::setResponseTimeout(60000);
```

## 5.0 Removal of all() Method from the Order, CustomsInfo, and CustomsItem Objects

Likelihood of Impact: Medium

The `/all` endpoint for the Order, CustomsInfo, and CustomsItem objects are not paginated and have therefore been removed from the library.

## 5.0 Removal of get_rates() Shipment Method

Likelihood of Impact: Medium

The HTTP method used for the `get_rates` endpoint at the API level has changed from `POST` to `GET` and will only retrieve rates for a shipment instead of regenerating them. A new `/rerate` endpoint has been introduced to replace this functionality; In this library, you can now call the `Shipment::regenerate_rates` method to regenerate rates. Due to the logic change, the `get_rates` method has been removed since a Shipment inherently already has rates associated.

## 5.0 Removal of all() Method from the Parcel Object

Likelihood of Impact: Low

There is no `/all` endpoint for the Parcel object. This function was removed as it was unusable.


## Upgrading from 3.x to 4.0

### 4.0 High Impact Changes

* [JSON Encoded Bodies](#40-json-encoded-bodies)

### 4.0 Low Impact Changes

* [Updating Dependencies](#40-updating-dependencies)

## 4.0 JSON Encoded Bodies

Likelihood of Impact: High

All `POST` and `PUT` request bodies are now JSON encoded instead of form-encoded. You may see subtle inconsistencies to how certain data types were previously sent to the API. We have taken steps to mitigate and test against these edge cases.

## 4.0 Updating Dependencies

Likelihood of Impact: Low

**Dependencies**

All dependencies had their patch versions bumped.

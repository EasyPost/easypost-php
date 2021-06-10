### 3.6.0 2021-06-10

* Adds `SmartRate` functionality to the `Shipments` object (available by calling `get_smartrates()` on a shipment)

### 3.5.0 2020-08-09

* Added event retrieval methods
* Fixed a bug that did not allow carrier accounts to be deleted
* Fixed a bug that did not allow users to be deleted
* Removed various pieces of old code


### 3.4.5 2020-05-07

* Added "Refund" and "Shipment Invoice" to the prefix and type lists


### 3.4.4 2020-03-11

* Version bump with updated author


### 3.4.3 2020-03-10

* Bumped version in package.json for Packagist
* Updated README with release instructions


### 3.4.2 2020-03-09

* Fixed a bug that would not create reports properly
* Added unit tests for reports


### 3.4.1 2018-04-13

* Added ability to set connection and response timeouts for all EasyPost actions (thanks mattjanssen!)


### 3.4.0 2017-11-30

* Renamed `Object` class `EasyPostObject` since Object is a keyword in PHP7.2


### 3.3.4 2017-05-24

* Fixed confusing Pickup example
* Enabled Reports to retrieve without a type parameter
* Use X-Client-User-Agent over X-EasyPost-Client-User-Agent


### 3.3.3 2017-02-14

* Added get_rates method to Order objects


### 3.3.2 2017-01-24

* Fixed compatibility issues with older PHP versions and the Report class


### 3.3.1 2017-01-19

* Fixed Create for ScanForms


### 3.3.0 2017-01-17

* Added basic CRUD methods for Webhooks


### 3.2.1 2016-12-14

* Fixed error in Util.php they may have affected shipment_reports retrievals


### 3.2.0 2016-12-12

* Added ability to create Report objects for shipment, tracker and payment_log reports


### 3.1.3 2016-10-25

* Fixed warning message from Address Verify/VerifyStrict


### 3.1.2 2016-09-16

* Added create_list for trackers


### 3.1.1 2016-07-21

* Added Insurance support


### 3.1.0 2016-06-02

* Renamed "Resource" class to "EasypostResource" since Resource is reserved in PHP7 (thanks ccovey!)


### 3.0.3 2016-02-22

* Added ability to create top-level users


### 3.0.2 2016-01-29

* Added 'verify' and 'verify_strict' options to Address creation


### 3.0.1 2016-01-05

* Updated ReadMe (thanks browner12!)
* Added DocBlock annotations to all methods (thanks browner12!)


### 3.0.0 2015-12-18

* Fixed Shipment Retrieve by tracking_code


### 2.1.2 2015-11-04

* Added Tracker.all example, and updated some old examples


### 2.1.1 2015-07-01

* Removed cURL options for SSL version, timeout, and connect timeout. The
default values for these options are acceptable.


### 2.1.0 2015-04-10

* Added User and CarrierAccount resources and examples for each.
* Modified EasyPost Object setter to record the change in the top level parent
object rather than the object that the change was made in. This allows you to
change sub objects and then call save() on the parent to have all properties
saved.
* Removed EasyPost Object transientValues and made unsavedValues act as a
representation of the params to send on save, rather than a flag to indicate
if a property had been changed.
* Added support for EasyPost's new error format: {"error": {"message": "...",
"code": ..., "errors": {...}}} as well as a prettyPrint function to better
display errors in development.
* Added support for additional HTTP methods PATCH and PUT.
* EasyPost Objects are now Iterable.


### 2.0.12 2015-02-04

* Added pickup class
* Added pickup example


### 2.0.11 2014-11-04

* Added tracker to shipment buy response
* Updated tracker examples


### 2.0.10 2014-07-07

* Added Item, Container, and Order resources.


### 2.0.9 2014-04-08

* Updated the CA cert bundle.


### 2.0.8 2013-12-29

* Added the ability to add negative filters to Shipment->lowest_rate by starting them with an exclamation mark.


### 2.0.7 2013-11-08

* Added endpoint to create shipment from tracking code.


### 2.0.6 2013-10-21

* Added Event resource for webhooks.


### 2.0.5 2013-08-30

* API Addition: Batch.buy method added. Trigger the purchasing of labels after a successful Batch.create.
* API Addition: Batch.scan_form method added. Quickly generate a scan_form for an entire batch.


### 2.0.4 2013-07-31

* API Addition: Tracker objects added. Trackers can be used to register any tracking code with EasyPost webhooks.


### 2.0.3 2013-07-16

* Bug fix: error messages should no longer be truncated to their first character.


### 2.0.2 2013-07-05

* Added function to Address to all creating and verifying at the same time.


### 2.0.1 2013-07-01

* Add label function to Shipment to request specific label file_formats (pdf, epl2, zpl).
* Add insure function to Shipment. Add insurance to any shipment in one call!

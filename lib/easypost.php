<?php

// Require this file if you're not using composer's vendor/autoload

// Required PHP extensions
if (!function_exists('curl_init')) {
    throw new Exception('EasyPost needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
    throw new Exception('EasyPost needs the JSON PHP extension.');
}

// Config and Utilities
require(dirname(__FILE__) . '/EasyPost/EasyPost.php');
require(dirname(__FILE__) . '/EasyPost/Util.php');
require(dirname(__FILE__) . '/EasyPost/Error.php');

// Guts
require(dirname(__FILE__) . '/EasyPost/EasyPostObject.php');
require(dirname(__FILE__) . '/EasyPost/EasypostResource.php');
require(dirname(__FILE__) . '/EasyPost/Requestor.php');

// API Resources
require(dirname(__FILE__) . '/EasyPost/Address.php');
require(dirname(__FILE__) . '/EasyPost/Batch.php');
require(dirname(__FILE__) . '/EasyPost/Brand.php');
require(dirname(__FILE__) . '/EasyPost/CarrierAccount.php');
require(dirname(__FILE__) . '/EasyPost/CarrierDetail.php');
require(dirname(__FILE__) . '/EasyPost/CreditCard.php');
require(dirname(__FILE__) . '/EasyPost/CustomsInfo.php');
require(dirname(__FILE__) . '/EasyPost/CustomsItem.php');
require(dirname(__FILE__) . '/EasyPost/Beta/EndShipper.php');
require(dirname(__FILE__) . '/EasyPost/Event.php');
require(dirname(__FILE__) . '/EasyPost/Fee.php');
require(dirname(__FILE__) . '/EasyPost/FieldError.php');
require(dirname(__FILE__) . '/EasyPost/Insurance.php');
require(dirname(__FILE__) . '/EasyPost/Message.php');
require(dirname(__FILE__) . '/EasyPost/Order.php');
require(dirname(__FILE__) . '/EasyPost/Parcel.php');
require(dirname(__FILE__) . '/EasyPost/PaymentMethod.php');
require(dirname(__FILE__) . '/EasyPost/Pickup.php');
require(dirname(__FILE__) . '/EasyPost/PickupRate.php');
require(dirname(__FILE__) . '/EasyPost/PostageLabel.php');
require(dirname(__FILE__) . '/EasyPost/Rate.php');
require(dirname(__FILE__) . '/EasyPost/Refund.php');
require(dirname(__FILE__) . '/EasyPost/Report.php');
require(dirname(__FILE__) . '/EasyPost/ScanForm.php');
require(dirname(__FILE__) . '/EasyPost/Shipment.php');
require(dirname(__FILE__) . '/EasyPost/TaxIdentifier.php');
require(dirname(__FILE__) . '/EasyPost/Tracker.php');
require(dirname(__FILE__) . '/EasyPost/TrackingDetail.php');
require(dirname(__FILE__) . '/EasyPost/TrackingLocation.php');
require(dirname(__FILE__) . '/EasyPost/User.php');
require(dirname(__FILE__) . '/EasyPost/Verification.php');
require(dirname(__FILE__) . '/EasyPost/VerificationDetails.php');
require(dirname(__FILE__) . '/EasyPost/Verifications.php');
require(dirname(__FILE__) . '/EasyPost/Webhook.php');

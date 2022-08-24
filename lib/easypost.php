<?php

// `require` this file if you're not using composer's vendor/autoload

// Required PHP extensions
if (!function_exists('curl_init')) {
    throw new Exception('EasyPost needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
    throw new Exception('EasyPost needs the JSON PHP extension.');
}

// Config and Utilities
require_once(dirname(__FILE__) . '/EasyPost/EasyPost.php');
require_once(dirname(__FILE__) . '/EasyPost/Util.php');
require_once(dirname(__FILE__) . '/EasyPost/Error.php');

// Guts
require_once(dirname(__FILE__) . '/EasyPost/EasyPostObject.php');
require_once(dirname(__FILE__) . '/EasyPost/EasypostResource.php');
require_once(dirname(__FILE__) . '/EasyPost/Requestor.php');

// API Resources
require_once(dirname(__FILE__) . '/EasyPost/Address.php');
require_once(dirname(__FILE__) . '/EasyPost/Batch.php');
require_once(dirname(__FILE__) . '/EasyPost/Brand.php');
require_once(dirname(__FILE__) . '/EasyPost/CarbonOffset.php');
require_once(dirname(__FILE__) . '/EasyPost/CarrierAccount.php');
require_once(dirname(__FILE__) . '/EasyPost/CarrierDetail.php');
require_once(dirname(__FILE__) . '/EasyPost/CustomsInfo.php');
require_once(dirname(__FILE__) . '/EasyPost/CustomsItem.php');
require_once(dirname(__FILE__) . '/EasyPost/Beta/EndShipper.php');
require_once(dirname(__FILE__) . '/EasyPost/EndShipper.php');
require_once(dirname(__FILE__) . '/EasyPost/Event.php');
require_once(dirname(__FILE__) . '/EasyPost/Fee.php');
require_once(dirname(__FILE__) . '/EasyPost/FieldError.php');
require_once(dirname(__FILE__) . '/EasyPost/Insurance.php');
require_once(dirname(__FILE__) . '/EasyPost/Message.php');
require_once(dirname(__FILE__) . '/EasyPost/Order.php');
require_once(dirname(__FILE__) . '/EasyPost/Parcel.php');
require_once(dirname(__FILE__) . '/EasyPost/Pickup.php');
require_once(dirname(__FILE__) . '/EasyPost/PickupRate.php');
require_once(dirname(__FILE__) . '/EasyPost/PostageLabel.php');
require_once(dirname(__FILE__) . '/EasyPost/Rate.php');
require_once(dirname(__FILE__) . '/EasyPost/Refund.php');
require_once(dirname(__FILE__) . '/EasyPost/Report.php');
require_once(dirname(__FILE__) . '/EasyPost/ScanForm.php');
require_once(dirname(__FILE__) . '/EasyPost/Shipment.php');
require_once(dirname(__FILE__) . '/EasyPost/TaxIdentifier.php');
require_once(dirname(__FILE__) . '/EasyPost/Tracker.php');
require_once(dirname(__FILE__) . '/EasyPost/TrackingDetail.php');
require_once(dirname(__FILE__) . '/EasyPost/TrackingLocation.php');
require_once(dirname(__FILE__) . '/EasyPost/User.php');
require_once(dirname(__FILE__) . '/EasyPost/Verification.php');
require_once(dirname(__FILE__) . '/EasyPost/VerificationDetails.php');
require_once(dirname(__FILE__) . '/EasyPost/Verifications.php');
require_once(dirname(__FILE__) . '/EasyPost/Webhook.php');

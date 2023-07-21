<?php

// `require` this file if you're not using composer's vendor/autoload

// Required PHP extensions
if (!function_exists('json_decode')) {
    throw new Exception('EasyPost needs the JSON PHP extension.');
}

// Exception Base Classes
require_once(dirname(__FILE__) . '/EasyPost/Exception/General/EasyPostException.php');
require_once(dirname(__FILE__) . '/EasyPost/Exception/Api/ApiException.php');

// API Exceptions
require_once(dirname(__FILE__) . '/EasyPost/Exception/Api/ApiException.php');
require_once(dirname(__FILE__) . '/EasyPost/Exception/Api/BadRequestException.php');
require_once(dirname(__FILE__) . '/EasyPost/Exception/Api/EncodingException.php');
require_once(dirname(__FILE__) . '/EasyPost/Exception/Api/ExternalApiException.php');
require_once(dirname(__FILE__) . '/EasyPost/Exception/Api/ForbiddenException.php');
require_once(dirname(__FILE__) . '/EasyPost/Exception/Api/GatewayTimeoutException.php');
require_once(dirname(__FILE__) . '/EasyPost/Exception/Api/HttpException.php');
require_once(dirname(__FILE__) . '/EasyPost/Exception/Api/InternalServerException.php');
require_once(dirname(__FILE__) . '/EasyPost/Exception/Api/InvalidRequestException.php');
require_once(dirname(__FILE__) . '/EasyPost/Exception/Api/JsonException.php');
require_once(dirname(__FILE__) . '/EasyPost/Exception/Api/MethodNotAllowedException.php');
require_once(dirname(__FILE__) . '/EasyPost/Exception/Api/NotFoundException.php');
require_once(dirname(__FILE__) . '/EasyPost/Exception/Api/PaymentException.php');
require_once(dirname(__FILE__) . '/EasyPost/Exception/Api/RateLimitException.php');
require_once(dirname(__FILE__) . '/EasyPost/Exception/Api/RedirectException.php');
require_once(dirname(__FILE__) . '/EasyPost/Exception/Api/ServiceUnavailableException.php');
require_once(dirname(__FILE__) . '/EasyPost/Exception/Api/TimeoutException.php');
require_once(dirname(__FILE__) . '/EasyPost/Exception/Api/UnauthorizedException.php');
require_once(dirname(__FILE__) . '/EasyPost/Exception/Api/UnknownApiException.php');

// General Exceptions
require_once(dirname(__FILE__) . '/EasyPost/Exception/General/FilteringException.php');
require_once(dirname(__FILE__) . '/EasyPost/Exception/General/InvalidObjectException.php');
require_once(dirname(__FILE__) . '/EasyPost/Exception/General/InvalidParameterException.php');
require_once(dirname(__FILE__) . '/EasyPost/Exception/General/MissingParameterException.php');
require_once(dirname(__FILE__) . '/EasyPost/Exception/General/SignatureVerificationException.php');

// Constants
require_once(dirname(__FILE__) . '/EasyPost/Constant/Constants.php');

// HTTP
require_once(dirname(__FILE__) . '/EasyPost/Http/Requestor.php');

// Utils
require_once(dirname(__FILE__) . '/EasyPost/Util/InternalUtil.php');
require_once(dirname(__FILE__) . '/EasyPost/Util/Util.php');

// EasyPost Lib (order is important for this section)
require_once(dirname(__FILE__) . '/EasyPost/Service/BaseService.php');
require_once(dirname(__FILE__) . '/EasyPost/EasyPostClient.php');
require_once(dirname(__FILE__) . '/EasyPost/EasyPostObject.php');

// Models
require_once(dirname(__FILE__) . '/EasyPost/Address.php');
require_once(dirname(__FILE__) . '/EasyPost/ApiKey.php');
require_once(dirname(__FILE__) . '/EasyPost/Batch.php');
require_once(dirname(__FILE__) . '/EasyPost/Brand.php');
require_once(dirname(__FILE__) . '/EasyPost/CarbonOffset.php');
require_once(dirname(__FILE__) . '/EasyPost/CarrierAccount.php');
require_once(dirname(__FILE__) . '/EasyPost/CarrierDetail.php');
require_once(dirname(__FILE__) . '/EasyPost/CustomsInfo.php');
require_once(dirname(__FILE__) . '/EasyPost/CustomsItem.php');
require_once(dirname(__FILE__) . '/EasyPost/EndShipper.php');
require_once(dirname(__FILE__) . '/EasyPost/Event.php');
require_once(dirname(__FILE__) . '/EasyPost/Fee.php');
require_once(dirname(__FILE__) . '/EasyPost/FieldError.php');
require_once(dirname(__FILE__) . '/EasyPost/Insurance.php');
require_once(dirname(__FILE__) . '/EasyPost/Message.php');
require_once(dirname(__FILE__) . '/EasyPost/Order.php');
require_once(dirname(__FILE__) . '/EasyPost/Parcel.php');
require_once(dirname(__FILE__) . '/EasyPost/Payload.php');
require_once(dirname(__FILE__) . '/EasyPost/Pickup.php');
require_once(dirname(__FILE__) . '/EasyPost/PickupRate.php');
require_once(dirname(__FILE__) . '/EasyPost/PostageLabel.php');
require_once(dirname(__FILE__) . '/EasyPost/Rate.php');
require_once(dirname(__FILE__) . '/EasyPost/ReferralCustomer.php');
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

// Services
require_once(dirname(__FILE__) . '/EasyPost/Service/AddressService.php');
require_once(dirname(__FILE__) . '/EasyPost/Service/BatchService.php');
require_once(dirname(__FILE__) . '/EasyPost/Service/BillingService.php');
require_once(dirname(__FILE__) . '/EasyPost/Service/CarrierAccountService.php');
require_once(dirname(__FILE__) . '/EasyPost/Service/CustomsInfoService.php');
require_once(dirname(__FILE__) . '/EasyPost/Service/CustomsItemService.php');
require_once(dirname(__FILE__) . '/EasyPost/Service/EndShipperService.php');
require_once(dirname(__FILE__) . '/EasyPost/Service/EventService.php');
require_once(dirname(__FILE__) . '/EasyPost/Service/InsuranceService.php');
require_once(dirname(__FILE__) . '/EasyPost/Service/OrderService.php');
require_once(dirname(__FILE__) . '/EasyPost/Service/ParcelService.php');
require_once(dirname(__FILE__) . '/EasyPost/Service/PickupService.php');
require_once(dirname(__FILE__) . '/EasyPost/Service/RateService.php');
require_once(dirname(__FILE__) . '/EasyPost/Service/ReferralCustomerService.php');
require_once(dirname(__FILE__) . '/EasyPost/Service/RefundService.php');
require_once(dirname(__FILE__) . '/EasyPost/Service/ReportService.php');
require_once(dirname(__FILE__) . '/EasyPost/Service/ScanFormService.php');
require_once(dirname(__FILE__) . '/EasyPost/Service/ShipmentService.php');
require_once(dirname(__FILE__) . '/EasyPost/Service/TrackerService.php');
require_once(dirname(__FILE__) . '/EasyPost/Service/UserService.php');
require_once(dirname(__FILE__) . '/EasyPost/Service/WebhookService.php');

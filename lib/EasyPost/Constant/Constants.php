<?php

namespace EasyPost\Constant;

abstract class Constants
{
    // EasyPostClient defaults
    const TIMEOUT = 60.0;
    const API_BASE = 'https://api.easypost.com';
    const API_VERSION = 'v2';
    const BETA_API_VERSION = 'beta';

    // Library constants
    const LIBRARY_VERSION = '6.9.1';
    const SUPPORT_EMAIL = 'support@easypost.com';

    // Validation
    const CARRIER_ACCOUNT_TYPES_WITH_CUSTOM_WORKFLOWS = [
        'FedexAccount',
        'UpsAccount',
        'FedexSmartpostAccount'
    ];

    // Exception messages (many of these are intended to be used with `sprintf()`)
    const ARRAY_REQUIRED_ERROR = 'You must pass an array as the first argument to EasyPost API method calls.';
    const COMMUNICATION_ERROR = 'Unexpected error communicating with %s. If this problem persists please let us know at ' . self::SUPPORT_EMAIL . '. %s'; // phpcs:ignore
    const DECODE_WEBHOOK_ERROR = 'There was a problem decoding the webhook.';
    const INVALID_PARAMETER_ERROR_WITH_SUGGESTION = 'Invalid %s value, must be one of: %s';
    const INVALID_PAYMENT_METHOD_ERROR = 'The chosen payment method is not valid. Please try again.';
    const INVALID_SIGNATURE_ERROR = 'Webhook received does not contain an HMAC signature.';
    const INVALID_WEBHOOK_VALIDATION_ERROR = 'Webhook received did not originate from EasyPost or had a webhook secret mismatch.'; // phpcs:ignore
    const MISSING_PARAMETER_ERROR = '%s is required.';
    const NO_BILLING_ERROR = 'Billing has not been setup for this user. Please add a payment method.';
    const NO_ID_URL_ERROR = 'Could not determine which URL to request: %s instance has invalid ID: %s';
    const NO_RATES_ERROR = 'No rates found.';

    const NO_USER_FOUND_ERROR = 'No user found with the given ID.';
    const NO_RESPONSE_ERROR = 'Did not receive a response from %s.';
    const SEND_STRIPE_DETAILS_ERROR = 'Could not send card details to Stripe, please try again later.';
    const UNDEFINED_PROPERTY_ERROR = 'EasyPost Notice: Undefined property of %s instance: %s';
    const NO_MATCHING_MOCK_REQUEST = 'No matching mock request found for %s %s';
    const END_OF_PAGINATION = 'There are no more pages to retrieve.';
}

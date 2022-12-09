<?php

namespace EasyPost\Constant;

abstract class Constant
{
    // EasyPostClient defaults
    const TIMEOUT = 60.0;
    const API_BASE = 'https://api.easypost.com';
    const API_VERSION = 'v2';

    // Library constants
    const LIBRARY_VERSION = '6.0.0';
    const SUPPORT_EMAIL = 'support@easypost.com';

    // Validation
    const CARRIER_ACCOUNT_TYPES_WITH_CUSTOM_WORKFLOWS = [
        'FedexAccount',
        'UpsAccount'
    ];
}

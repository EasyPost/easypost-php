<?php

namespace EasyPost\Constant;

abstract class Constant
{
    const TIMEOUT = 60.0;
    const API_BASE = 'https://api.easypost.com/' . self::API_VERSION;

    const API_VERSION = 'v2';
    const BETA_API_BASE = 'https://api.easypost.com/beta';
    const LIBRARY_VERSION = '6.0.0';
    const SUPPORT_EMAIL = 'support@easypost.com';

    const CARRIER_ACCOUNT_TYPES_WITH_CUSTOM_WORKFLOWS = [
        'FedexAccount',
        'UpsAccount'
    ];
}

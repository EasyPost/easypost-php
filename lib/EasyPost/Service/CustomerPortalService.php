<?php

namespace EasyPost\Service;

use EasyPost\Http\Requestor;
use EasyPost\Util\InternalUtil;

/**
 * CustomerPortal service containing all the logic to make API calls.
 */
class CustomerPortalService extends BaseService
{
    /**
     * Create a Portal Session.
     *
     * @param mixed $params
     * @return mixed
     */
    public function createAccountLink(mixed $params = null): mixed
    {
        $response = Requestor::request($this->client, 'post', '/customer_portal/account_link', $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }
}

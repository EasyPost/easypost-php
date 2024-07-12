<?php

namespace EasyPost\Service;

use EasyPost\Exception\General\EndOfPaginationException;
use EasyPost\Http\Requestor;
use EasyPost\Util\InternalUtil;

/**
 * SmartRate service containing all the details of SmartRate requests.
 */
class SmartRateService extends BaseService
{
    /**
     * Retrieve a recommended ship date for each carrier-service level combination via the
     * Smart Deliver On API, based on a specific delivery date and origin-destination postal code pair.
     *
     * @param mixed $params
     * @return mixed
     */
    public function recommendShipDate(mixed $params = null): mixed
    {
        $response = Requestor::request($this->client, 'post', '/smartrate/deliver_on', $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Retrieve the estimated delivery date of each carrier-service level combination via the
     * Smart Deliver By API, based on a specific ship date and origin-destination postal code pair.
     *
     * @param mixed $params
     * @return mixed
     */
    public function estimateDeliveryDate(mixed $params = null): mixed
    {
        $response = Requestor::request($this->client, 'post', '/smartrate/deliver_by', $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }
}

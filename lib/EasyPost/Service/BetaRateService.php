<?php

namespace EasyPost\Service;

use EasyPost\Http\Requestor;
use EasyPost\Util\InternalUtil;

/**
 * Rate service containing all the logic to make API calls.
 */
class BetaRateService extends BaseService
{
    /**
     * Retreive stateless rates.
     *
     * @param mixed $params
     * @return mixed
     */
    public function retrieveStatelessRates($params)
    {
        $wrappedParams = [
            'shipment' => $params,
        ];

        $response = Requestor::request($this->client, 'post', '/rates', $wrappedParams, true);

        return InternalUtil::convertToEasyPostObject($this->client, $response['rates']);
    }
}

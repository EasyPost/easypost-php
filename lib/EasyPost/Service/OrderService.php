<?php

namespace EasyPost\Service;

use EasyPost\Http\Requestor;
use EasyPost\Rate;
use EasyPost\Util\InternalUtil;

/**
 * Order service containing all the logic to make API calls.
 */
class OrderService extends BaseService
{
    /**
     * Retrieve an order.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve(string $id): mixed
    {
        return self::retrieveResource(self::serviceModelClassName(self::class), $id);
    }

    /**
     * Create an order.
     *
     * @param mixed $params
     * @return mixed
     */
    public function create(mixed $params = null): mixed
    {
        $params = InternalUtil::wrapParams($params, 'order');

        return self::createResource(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Get rates for a order.
     *
     * @param string $id
     * @param mixed $params
     * @return mixed
     */
    public function getRates(string $id, mixed $params = null): mixed
    {
        $url = $this->instanceUrl(self::serviceModelClassName(self::class), $id) . '/rates';
        $response = Requestor::request($this->client, 'get', $url, $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Buy an order.
     *
     * @param string $id
     * @param mixed $params
     * @return mixed
     */
    public function buy(string $id, mixed $params = null): mixed
    {
        // Don't use InternalUtil::wrapParams here
        if ($params instanceof Rate) {
            $clone = $params;
            unset($params);
            $params['carrier'] = $clone->carrier;
            $params['service'] = $clone->service;
        }

        $url = $this->instanceUrl(self::serviceModelClassName(self::class), $id) . '/buy';
        $response = Requestor::request($this->client, 'post', $url, $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }
}

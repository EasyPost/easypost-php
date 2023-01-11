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
    public function retrieve($id)
    {
        return self::retrieveResource(self::serviceModelClassName(self::class), $id);
    }

    /**
     * Create an order.
     *
     * @param mixed $params
     * @return mixed
     */
    public function create($params = null)
    {
        if (!isset($params['order']) || !is_array($params['order'])) {
            $clone = $params;
            unset($params);
            $params['order'] = $clone;
        }

        return self::createResource(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Get rates for a order.
     *
     * @param string $id
     * @param mixed $params
     * @return mixed
     */
    public function getRates($id, $params = null)
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
    public function buy($id, $params = null)
    {
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

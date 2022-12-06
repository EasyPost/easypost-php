<?php

namespace EasyPost\Service;

use EasyPost\Http\Requestor;
use EasyPost\Rate;
use EasyPost\Util\Util;

/**
 * Order service containing all the logic to make API calls.
 */
class OrderService extends BaseService
{
    private static $modelClass = 'Order';

    /**
     * Retrieve an order.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve($id)
    {
        return self::retrieveResource(self::$modelClass, $id);
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

        return self::createResource(self::$modelClass, $params);
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
        $requestor = new Requestor($this->client);
        $url = $this->instanceUrl(self::$modelClass, $id) . '/rates';
        $response = $requestor->request('get', $url, $params);

        return Util::convertToEasyPostObject($this->client, $response);
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
        $requestor = new Requestor($this->client);
        $url = $this->instanceUrl(self::$modelClass, $id) . '/buy';

        if ($params instanceof Rate) {
            $clone = $params;
            unset($params);
            $params['carrier'] = $clone->carrier;
            $params['service'] = $clone->service;
        }

        $response = $requestor->request('post', $url, $params);

        return Util::convertToEasyPostObject($this->client, $response);
    }
}

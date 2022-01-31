<?php

namespace EasyPost;

/**
 * @package EasyPost
 * @property string $id
 * @property string $object
 * @property string $reference
 * @property string $mode
 * @property Address $to_address
 * @property Address $from_address
 * @property Address $return_address
 * @property Address $buyer_address
 * @property Shipment[] $shipments
 * @property Rates[] $rates
 * @property Message[] $messages
 * @property bool $is_return
 * @property string $created_at
 * @property string $updated_at
 */
class Order extends EasypostResource
{
    /**
     * Retrieve an order.
     *
     * @param string $id
     * @param string $apiKey
     * @return mixed
     */
    public static function retrieve($id, $apiKey = null)
    {
        return self::_retrieve(get_class(), $id, $apiKey);
    }

    /**
     * Retrieve all orders.
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return mixed
     */
    public static function all($params = null, $apiKey = null)
    {
        return self::_all(get_class(), $params, $apiKey);
    }

    /**
     * Create an order.
     *
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        if (!isset($params['order']) || !is_array($params['order'])) {
            $clone = $params;
            unset($params);
            $params['order'] = $clone;
        }

        return self::_create(get_class(), $params, $apiKey);
    }

    /**
     * Get rates for a order.
     *
     * @param mixed $params
     * @return $this
     * @throws \EasyPost\Error
     */
    public function get_rates($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/rates';
        list($response, $apiKey) = $requestor->request('get', $url, $params);
        $this->refreshFrom($response, $apiKey, true);

        return $this;
    }

    /**
     * Buy an order.
     *
     * @param mixed $params
     * @return $this
     * @throws \EasyPost\Error
     */
    public function buy($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/buy';

        if ($params instanceof Rate) {
            $clone = $params;
            unset($params);
            $params['carrier'] = $clone->carrier;
            $params['service'] = $clone->service;
        }

        list($response, $apiKey) = $requestor->request('post', $url, $params);
        $this->refreshFrom($response, $apiKey, false);

        return $this;
    }
}

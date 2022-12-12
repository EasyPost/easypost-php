<?php

namespace EasyPost\Service;

use EasyPost\Http\Requestor;
use EasyPost\Util\InternalUtil;
use EasyPost\Util\Util;

/**
 * Shipment service containing all the logic to make API calls.
 */
class ShipmentService extends BaseService
{
    private static $modelClass = 'Shipment';
    /**
     * Retrieve a shipment.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve($id)
    {
        return self::retrieveResource(self::$modelClass, $id);
    }

    /**
     * Retrieve all shipments.
     *
     * @param mixed $params
     * @return mixed
     */
    public function all($params = null)
    {
        return self::allResources(self::$modelClass, $params);
    }

    /**
     * Create a shipment.
     *
     * @param mixed $params
     * @param bool $withCarbonOffset
     * @return mixed
     */
    public function create($params = null, $withCarbonOffset = false)
    {
        if (!isset($params['shipment']) || !is_array($params['shipment'])) {
            $clone = $params;
            unset($params);
            $params['shipment'] = $clone;
        }

        $params['carbon_offset'] = $withCarbonOffset;

        return self::createResource(self::$modelClass, $params);
    }

    /**
     * Re-rate a shipment.
     *
     * @param string $id
     * @param mixed $params
     * @param bool $withCarbonOffset
     * @return mixed
     */
    public function regenerateRates($id, $params = null, $withCarbonOffset = false)
    {
        $requestor = new Requestor($this->client);
        $url = $this->instanceUrl(self::$modelClass, $id) . '/rerate';
        $params['carbon_offset'] = $withCarbonOffset;
        $response = $requestor->request('post', $url, $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Get SmartRates for a shipment.
     *
     * @param string $id
     * @return array
     */
    public function getSmartRates($id)
    {
        $requestor = new Requestor($this->client);
        $url = $this->instanceUrl(self::$modelClass, $id) . '/smartrate';
        $response = $requestor->request('get', $url);

        $result = isset($response['result']) ? $response['result'] : [];

        return InternalUtil::convertToEasyPostObject($this->client, $result);
    }

    /**
     * Buy a shipment.
     *
     * @param string $id
     * @param mixed $params
     * @param boolean $withCarbonOffset
     * @param string $endShipperId
     * @return mixed
     */
    public function buy($id, $params = null, $withCarbonOffset = false, $endShipperId = false)
    {
        $requestor = new Requestor($this->client);
        $url = $this->instanceUrl(self::$modelClass, $id) . '/buy';

        if (isset($params['id']) && (!isset($params['rate']) || !is_array($params['rate']))) {
            $clone = $params;
            unset($params);
            $params['rate'] = $clone;
        }

        $params['carbon_offset'] = $withCarbonOffset;

        if ($endShipperId !== false) {
            $params['end_shipper_id'] = $endShipperId;
        }

        $response = $requestor->request('post', $url, $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Refund a shipment.
     *
     * @param string $id
     * @param mixed $params
     * @return mixed
     */
    public function refund($id, $params = null)
    {
        $requestor = new Requestor($this->client);
        $url = $this->instanceUrl(self::$modelClass, $id) . '/refund';

        $response = $requestor->request('post', $url, $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Convert the label format of the shipment.
     *
     * @param string $id
     * @param mixed $params
     * @return mixed
     */
    public function label($id, $params = null)
    {
        $requestor = new Requestor($this->client);
        $url = $this->instanceUrl(self::$modelClass, $id) . '/label';

        if (!isset($params['file_format'])) {
            $clone = $params;
            unset($params);
            $params['file_format'] = $clone;
        }

        $response = $requestor->request('get', $url, $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Insure the shipment.
     *
     * @param string $id
     * @param mixed $params
     * @return mixed
     */
    public function insure($id, $params = null)
    {
        $requestor = new Requestor($this->client);
        $url = $this->instanceUrl(self::$modelClass, $id) . '/insure';

        if (!isset($params['amount'])) {
            $clone = $params;
            unset($params);
            $params['amount'] = $clone;
        }

        $response = $requestor->request('post', $url, $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Generate a form for the shipment.
     *
     * @param string $id
     * @param string $formType
     * @param mixed $formOptions
     * @return mixed
     */
    public function generateForm($id, $formType, $formOptions = null)
    {
        $requestor = new Requestor($this->client);
        $url = $this->instanceUrl(self::$modelClass, $id) . '/forms';
        $formOptions['type'] = $formType;

        $params['form'] = $formOptions;

        $response = $requestor->request('post', $url, $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Get the lowest SmartRate of the shipment.
     *
     * To exclude a carrier or service, prepend the string with `!`.
     *
     * @param string $id
     * @param int $deliveryDays
     * @param string $deliveryAccuracy
     * @return Rate
     */
    public function lowestSmartRate($id, $deliveryDays, $deliveryAccuracy)
    {
        $smartRates = self::getSmartRates($id);
        $lowestRate = Util::getLowestSmartRate($smartRates, $deliveryDays, strtolower($deliveryAccuracy));

        return $lowestRate;
    }
}

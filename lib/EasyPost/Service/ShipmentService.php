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
    /**
     * Retrieve a shipment.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve($id)
    {
        return self::retrieveResource(self::serviceModelClassName(self::class), $id);
    }

    /**
     * Retrieve all shipments.
     *
     * @param mixed $params
     * @return mixed
     */
    public function all($params = null)
    {
        self::validate($params);
        $response = Requestor::request($this->client, 'get', '/shipments', $params);
        $response['purchased'] = $params['purchased'] ?? null;
        $response['include_children'] = $params['include_children'] ?? null;

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Retrieve the next page of Shipment collection
     *
     * @param mixed $shipments
     * @param string $pageSize
     * @return mixed
     */
    public function getNextPage($shipments, $pageSize = null)
    {
        $params = [];

        if (isset($shipments->purchased)) {
            $params['purchased'] = $shipments->purchased;
        }

        if (isset($shipments->include_children)) {
            $params['include_children'] = $shipments->include_children;
        }
        return $this->getNextPageResources(self::serviceModelClassName(self::class), $shipments, $pageSize, $params);
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

        return self::createResource(self::serviceModelClassName(self::class), $params);
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
        $url = $this->instanceUrl(self::serviceModelClassName(self::class), $id) . '/rerate';
        $params['carbon_offset'] = $withCarbonOffset;
        $response = Requestor::request($this->client, 'post', $url, $params);

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
        $url = $this->instanceUrl(self::serviceModelClassName(self::class), $id) . '/smartrate';
        $response = Requestor::request($this->client, 'get', $url);

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
        if (isset($params['id']) && (!isset($params['rate']) || !is_array($params['rate']))) {
            $clone = $params;
            unset($params);
            $params['rate'] = $clone;
        }

        $url = $this->instanceUrl(self::serviceModelClassName(self::class), $id) . '/buy';
        $params['carbon_offset'] = $withCarbonOffset;

        if ($endShipperId !== false) {
            $params['end_shipper_id'] = $endShipperId;
        }

        $response = Requestor::request($this->client, 'post', $url, $params);

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
        $url = $this->instanceUrl(self::serviceModelClassName(self::class), $id) . '/refund';
        $response = Requestor::request($this->client, 'post', $url, $params);

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
        if (!isset($params['file_format'])) {
            $clone = $params;
            unset($params);
            $params['file_format'] = $clone;
        }

        $url = $this->instanceUrl(self::serviceModelClassName(self::class), $id) . '/label';
        $response = Requestor::request($this->client, 'get', $url, $params);

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
        if (!isset($params['amount'])) {
            $clone = $params;
            unset($params);
            $params['amount'] = $clone;
        }

        $url = $this->instanceUrl(self::serviceModelClassName(self::class), $id) . '/insure';
        $response = Requestor::request($this->client, 'post', $url, $params);

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
        $url = $this->instanceUrl(self::serviceModelClassName(self::class), $id) . '/forms';
        $formOptions['type'] = $formType;

        $params['form'] = $formOptions;

        $response = Requestor::request($this->client, 'post', $url, $params);

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

    /**
     * Retrieves the estimated delivery date of each Rate via SmartRate.
     *
     * @param string $id
     * @param string $plannedShipDate
     * @return mixed
     */
    public function retrieveEstimatedDeliveryDate($id, $plannedShipDate)
    {
        $params = [
            'planned_ship_date' => $plannedShipDate,
        ];

        $url = $this->instanceUrl(self::serviceModelClassName(self::class), $id) . '/smartrate/delivery_date';
        $response = Requestor::request($this->client, 'get', $url, $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response['rates'] ?? []);
    }
}

<?php

namespace EasyPost\Service;

use EasyPost\Http\Requestor;
use EasyPost\Rate;
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
    public function retrieve(string $id): mixed
    {
        return self::retrieveResource(self::serviceModelClassName(self::class), $id);
    }

    /**
     * Retrieve all shipments.
     *
     * @param mixed $params
     * @return mixed
     */
    public function all(mixed $params = null): mixed
    {
        return self::allResources(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Retrieve the next page of Shipment collection
     *
     * @param mixed $shipments
     * @param int|null $pageSize
     * @return mixed
     */
    public function getNextPage(mixed $shipments, ?int $pageSize = null): mixed
    {
        return $this->getNextPageResources(self::serviceModelClassName(self::class), $shipments, $pageSize);
    }

    /**
     * Create a shipment.
     *
     * @param mixed $params
     * @return mixed
     */
    public function create(mixed $params = null): mixed
    {
        $params = InternalUtil::wrapParams($params, 'shipment');

        return self::createResource(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Re-rate a shipment.
     *
     * @param string $id
     * @param mixed $params
     * @return mixed
     */
    public function regenerateRates(string $id, mixed $params = null): mixed
    {
        $url = $this->instanceUrl(self::serviceModelClassName(self::class), $id) . '/rerate';
        $response = Requestor::request($this->client, 'post', $url, $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Get SmartRates for a shipment.
     *
     * @param string $id
     * @return array<mixed>
     */
    public function getSmartRates(string $id): array
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
     * @param string|bool $endShipperId
     * @return mixed
     */
    public function buy(string $id, mixed $params = null, string|bool $endShipperId = false)
    {
        // Don't use InternalUtil::wrapParams here
        if (isset($params['id']) && (!isset($params['rate']) || !is_array($params['rate']))) {
            $clone = $params;
            unset($params);
            $params['rate'] = $clone;
        }

        $url = $this->instanceUrl(self::serviceModelClassName(self::class), $id) . '/buy';

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
    public function refund(string $id, mixed $params = null): mixed
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
    public function label(string $id, mixed $params = null): mixed
    {
        // TODO: only accept wrapped params
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
    public function insure(string $id, mixed $params = null): mixed
    {
        // TODO: Only accept wrapped params
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
    public function generateForm(string $id, string $formType, mixed $formOptions = null): mixed
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
    public function lowestSmartRate(string $id, int $deliveryDays, string $deliveryAccuracy): Rate
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
    public function retrieveEstimatedDeliveryDate(string $id, string $plannedShipDate): mixed
    {
        $params = [
            'planned_ship_date' => $plannedShipDate,
        ];

        $url = $this->instanceUrl(self::serviceModelClassName(self::class), $id) . '/smartrate/delivery_date';
        $response = Requestor::request($this->client, 'get', $url, $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response['rates'] ?? []);
    }

    /**
     * Retrieve a recommended ship date for an existing Shipment via the Precision Shipping API,
     * based on a specific desired delivery date.
     *
     * @param string $id
     * @param string $desiredDeliveryDate
     * @return mixed
     */
    public function recommendShipDate(string $id, string $desiredDeliveryDate): mixed
    {
        $params = [
            'desired_delivery_date' => $desiredDeliveryDate,
        ];

        $url = $this->instanceUrl(self::serviceModelClassName(self::class), $id) . '/smartrate/precision_shipping';
        $response = Requestor::request($this->client, 'get', $url, $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response['rates'] ?? []);
    }

    /**
     * Create and buy a Luma Shipment in one call.
     *
     * @param array<mixed> $params
     * @return mixed
     */
    public function createAndBuyLuma(array $params): mixed
    {
        $params = InternalUtil::wrapParams($params, 'shipment');

        $url = self::classUrl(self::serviceModelClassName(self::class)) . '/luma';
        $response = Requestor::request($this->client, 'post', $url, $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Buy a Shipment with Luma.
     *
     * @param string $id
     * @param array<mixed> $params
     * @return mixed
     */
    public function buyLuma(string $id, array $params): mixed
    {
        $url = $this->instanceUrl(self::serviceModelClassName(self::class), $id) . '/luma';
        $response = Requestor::request($this->client, 'post', $url, $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }
}

<?php

namespace EasyPost\Service;

use EasyPost\Http\Requestor;
use EasyPost\Util\InternalUtil;

/**
 * Batch service containing all the logic to make API calls.
 */
class BatchService extends BaseService
{
    /**
     * Retrieve a batch.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve($id)
    {
        return self::retrieveResource(self::serviceModelClassName(self::class), $id);
    }

    /**
     * Retrieve all batches.
     *
     * @param mixed $params
     * @return mixed
     */
    public function all($params = null)
    {
        return self::allResources(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Create a batch.
     *
     * @param mixed $params
     * @return mixed
     */
    public function create($params = null)
    {
        if (!isset($params['batch']) || !is_array($params['batch'])) {
            $clone = $params;
            unset($params);
            $params['batch'] = $clone;
        }

        return self::createResource(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Create and buy a batch.
     *
     * @param mixed $params
     * @return mixed
     */
    public function createAndBuy($params = null)
    {
        if (!isset($params['batch']) || !is_array($params['batch'])) {
            $clone = $params;
            unset($params);

            $shipments = [];

            foreach ($clone as $index => $shipment) {
                $shipments[$index] = $shipment;
            }

            $params = [
                'batch' => [
                    'shipment' => $shipments,
                ],
            ];
        }

        $url = self::classUrl(self::serviceModelClassName(self::class));
        $response = Requestor::request($this->client, 'post', $url . '/create_and_buy', $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Buy a batch.
     *
     * @param string $id
     * @param mixed $params
     * @return mixed
     */
    public function buy($id, $params = null)
    {
        $url = $this->instanceUrl(self::serviceModelClassName(self::class), $id) . '/buy';
        $response = Requestor::request($this->client, 'post', $url, $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Create a batch label.
     *
     * @param string $id
     * @param mixed $params
     * @return mixed
     */
    public function label($id, $params = null)
    {
        $url = $this->instanceUrl(self::serviceModelClassName(self::class), $id) . '/label';
        $response = Requestor::request($this->client, 'post', $url, $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Remove shipments from a batch.
     *
     * @param string $id
     * @param mixed $params
     * @return mixed
     */
    public function removeShipments($id, $params = null)
    {
        $url = $this->instanceUrl(self::serviceModelClassName(self::class), $id) . '/remove_shipments';
        $response = Requestor::request($this->client, 'post', $url, $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Add shipments to a batch.
     *
     * @param string $id
     * @param mixed $params
     * @return mixed
     */
    public function addShipments($id, $params = null)
    {
        $url = $this->instanceUrl(self::serviceModelClassName(self::class), $id) . '/add_shipments';
        $response = Requestor::request($this->client, 'post', $url, $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Create a batch scanform.
     *
     * @param string $id
     * @param mixed $params
     * @return mixed
     */
    public function createScanForm($id, $params = null)
    {
        $url = $this->instanceUrl(self::serviceModelClassName(self::class), $id) . '/scan_form';
        $response = Requestor::request($this->client, 'post', $url, $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }
}

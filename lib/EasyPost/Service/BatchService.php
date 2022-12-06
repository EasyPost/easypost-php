<?php

namespace EasyPost\Service;

use EasyPost\Http\Requestor;
use EasyPost\Util\Util;

/**
 * Batch service containing all the logic to make API calls.
 */
class BatchService extends BaseService
{
    private static $modelClass = 'Batch';

    /**
     * Retrieve a batch.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve($id)
    {
        return self::retrieveResource(self::$modelClass, $id);
    }

    /**
     * Retrieve all batches.
     *
     * @param mixed $params
     * @return mixed
     */
    public function all($params = null)
    {
        return self::allResources(self::$modelClass, $params);
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

        return self::createResource(self::$modelClass, $params);
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

        $requestor = new Requestor($this->client);
        $url = self::classUrl(self::$modelClass);
        $response = $requestor->request('post', $url . '/create_and_buy', $params);

        return Util::convertToEasyPostObject($this->client, $response);
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
        $requestor = new Requestor($this->client);
        $url = $this->instanceUrl(self::$modelClass, $id) . '/buy';

        $response = $requestor->request('post', $url, $params);

        return Util::convertToEasyPostObject($this->client, $response);
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
        $requestor = new Requestor($this->client);
        $url = $this->instanceUrl(self::$modelClass, $id) . '/label';

        $response = $requestor->request('post', $url, $params);

        return Util::convertToEasyPostObject($this->client, $response);
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
        $requestor = new Requestor($this->client);
        $url = $this->instanceUrl(self::$modelClass, $id) . '/remove_shipments';

        $response = $requestor->request('post', $url, $params);

        return Util::convertToEasyPostObject($this->client, $response);
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
        $requestor = new Requestor($this->client);
        $url = $this->instanceUrl(self::$modelClass, $id) . '/add_shipments';

        $response = $requestor->request('post', $url, $params);

        return Util::convertToEasyPostObject($this->client, $response);
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
        $requestor = new Requestor($this->client);
        $url = $this->instanceUrl(self::$modelClass, $id) . '/scan_form';

        $response = $requestor->request('post', $url, $params);

        return Util::convertToEasyPostObject($this->client, $response);
    }
}

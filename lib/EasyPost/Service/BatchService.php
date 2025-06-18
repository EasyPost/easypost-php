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
    public function retrieve(string $id): mixed
    {
        return self::retrieveResource(self::serviceModelClassName(self::class), $id);
    }

    /**
     * Retrieve all batches.
     *
     * @param mixed $params
     * @return mixed
     */
    public function all(mixed $params = null): mixed
    {
        return self::allResources(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Create a batch.
     *
     * @param mixed $params
     * @return mixed
     */
    public function create(mixed $params = null): mixed
    {
        $params = InternalUtil::wrapParams($params, 'batch');

        return self::createResource(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Buy a batch.
     *
     * @param string $id
     * @param mixed $params
     * @return mixed
     */
    public function buy(string $id, mixed $params = null): mixed
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
    public function label(string $id, mixed $params = null): mixed
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
    public function removeShipments(string $id, mixed $params = null): mixed
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
    public function addShipments(string $id, mixed $params = null): mixed
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
    public function createScanForm(string $id, mixed $params = null): mixed
    {
        $url = $this->instanceUrl(self::serviceModelClassName(self::class), $id) . '/scan_form';
        $response = Requestor::request($this->client, 'post', $url, $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }
}

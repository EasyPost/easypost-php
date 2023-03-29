<?php

namespace EasyPost\Service;

use EasyPost\Http\Requestor;
use EasyPost\Util\InternalUtil;

/**
 * Pickup service containing all the logic to make API calls.
 */
class PickupService extends BaseService
{
    /**
     * Retrieve a pickup.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve($id)
    {
        return self::retrieveResource(self::serviceModelClassName(self::class), $id);
    }

    /**
     * Retrieve all pickups.
     *
     * @param mixed $params
     * @return mixed
     */
    public function all($params = null)
    {
        return self::allResources(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Retrieve the next page of Pickup collection
     *
     * @param mixed $pickups
     * @param string $pageSize
     * @return mixed
     */
    public function getNextPage($pickups, $pageSize = null)
    {
        return $this->getNextPageResources(self::serviceModelClassName(self::class), $pickups, $pageSize);
    }

    /**
     * Create a pickup.
     *
     * @param mixed $params
     * @return mixed
     */
    public function create($params = null)
    {
        if (!isset($params['pickup']) || !is_array($params['pickup'])) {
            $clone = $params;
            unset($params);
            $params['pickup'] = $clone;
        }

        return self::createResource(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Buy a pickup.
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
     * Cancel a pickup.
     *
     * @param string $id
     * @param mixed $params
     * @return mixed
     */
    public function cancel($id, $params = null)
    {
        $url = $this->instanceUrl(self::serviceModelClassName(self::class), $id) . '/cancel';
        $response = Requestor::request($this->client, 'post', $url, $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }
}

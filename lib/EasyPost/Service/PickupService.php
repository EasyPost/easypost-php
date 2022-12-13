<?php

namespace EasyPost\Service;

use EasyPost\Http\Requestor;
use EasyPost\Util\InternalUtil;

/**
 * Pickup service containing all the logic to make API calls.
 */
class PickupService extends BaseService
{
    private static $modelClass = 'Pickup';

    /**
     * Retrieve a pickup.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve($id)
    {
        return self::retrieveResource(self::$modelClass, $id);
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

        return self::createResource(self::$modelClass, $params);
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
        $url = $this->instanceUrl(self::$modelClass, $id) . '/buy';
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
        $url = $this->instanceUrl(self::$modelClass, $id) . '/cancel';
        $response = Requestor::request($this->client, 'post', $url, $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }
}

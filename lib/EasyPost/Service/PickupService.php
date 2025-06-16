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
    public function retrieve(string $id): mixed
    {
        return self::retrieveResource(self::serviceModelClassName(self::class), $id);
    }

    /**
     * Retrieve all pickups.
     *
     * @param mixed $params
     * @return mixed
     */
    public function all(mixed $params = null): mixed
    {
        return self::allResources(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Retrieve the next page of Pickup collection
     *
     * @param mixed $pickups
     * @param int|null $pageSize
     * @return mixed
     */
    public function getNextPage(mixed $pickups, ?int $pageSize = null): mixed
    {
        return $this->getNextPageResources(self::serviceModelClassName(self::class), $pickups, $pageSize);
    }

    /**
     * Create a pickup.
     *
     * @param mixed $params
     * @return mixed
     */
    public function create(mixed $params = null): mixed
    {
        $params = InternalUtil::wrapParams($params, 'pickup');

        return self::createResource(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Buy a pickup.
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
     * Cancel a pickup.
     *
     * @param string $id
     * @param mixed $params
     * @return mixed
     */
    public function cancel(string $id, mixed $params = null): mixed
    {
        $url = $this->instanceUrl(self::serviceModelClassName(self::class), $id) . '/cancel';
        $response = Requestor::request($this->client, 'post', $url, $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }
}

<?php

namespace EasyPost\Service;

use EasyPost\Util\InternalUtil;

/**
 * EndShipper service containing all the logic to make API calls.
 */
class EndShipperService extends BaseService
{
    /**
     * Create an EndShipper object.
     *
     * @param mixed $params
     * @return mixed
     */
    public function create(mixed $params = null): mixed
    {
        $params = InternalUtil::wrapParams($params, 'address');

        return self::createResource(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Retrieve an EndShipper object.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve(string $id): mixed
    {
        return self::retrieveResource(self::serviceModelClassName(self::class), $id);
    }

    /**
     * Retrieve all EndShipper objects.
     *
     * @param mixed $params
     * @return mixed
     */
    public function all(mixed $params = null): mixed
    {
        return self::allResources(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Update an EndShipper object.
     *
     * @param string $id
     * @param mixed $params
     * @return mixed
     */
    public function update(string $id, mixed $params): mixed
    {
        $params = InternalUtil::wrapParams($params, 'address');

        return self::updateResource(self::serviceModelClassName(self::class), $id, $params, 'put');
    }
}

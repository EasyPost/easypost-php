<?php

namespace EasyPost\Service;

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
    public function create($params = null)
    {
        if (!isset($params['address']) || !is_array($params['address'])) {
            $clone = $params;
            unset($params);
            $params['address'] = $clone;
        }

        return self::createResource(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Retrieve an EndShipper object.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve($id)
    {
        return self::retrieveResource(self::serviceModelClassName(self::class), $id);
    }

    /**
     * Retrieve all EndShipper objects.
     *
     * @param mixed $params
     * @return mixed
     */
    public function all($params = null)
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
    public function update($id, $params)
    {
        if (!isset($params['address']) || !is_array($params['address'])) {
            $clone = $params;
            unset($params);
            $params['address'] = $clone;
        }

        return self::updateResource(self::serviceModelClassName(self::class), $id, $params, 'put');
    }
}

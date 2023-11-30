<?php

namespace EasyPost\Service;

/**
 * Parcel service containing all the logic to make API calls.
 */
class ParcelService extends BaseService
{
    /**
     * Retrieve a parcel.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve(string $id): mixed
    {
        return self::retrieveResource(self::serviceModelClassName(self::class), $id);
    }

    /**
     * Create a parcel.
     *
     * @param mixed $params
     * @return mixed
     */
    public function create(mixed $params = null): mixed
    {
        if (!isset($params['parcel']) || !is_array($params['parcel'])) {
            $clone = $params;
            unset($params);
            $params['parcel'] = $clone;
        }

        return self::createResource(self::serviceModelClassName(self::class), $params);
    }
}

<?php

namespace EasyPost\Service;

/**
 * Parcel service containing all the logic to make API calls.
 */
class ParcelService extends BaseService
{
    private static $modelClass = 'Parcel';

    /**
     * Retrieve a parcel.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve($id)
    {
        return self::retrieveResource(self::$modelClass, $id);
    }

    /**
     * Create a parcel.
     *
     * @param mixed $params
     * @return mixed
     */
    public function create($params = null)
    {
        if (!isset($params['parcel']) || !is_array($params['parcel'])) {
            $clone = $params;
            unset($params);
            $params['parcel'] = $clone;
        }

        return self::createResource(self::$modelClass, $params);
    }
}

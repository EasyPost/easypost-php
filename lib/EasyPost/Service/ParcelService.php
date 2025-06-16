<?php

namespace EasyPost\Service;

use EasyPost\Util\InternalUtil;

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
        $params = InternalUtil::wrapParams($params, 'parcel');

        return self::createResource(self::serviceModelClassName(self::class), $params);
    }
}

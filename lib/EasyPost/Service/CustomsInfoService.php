<?php

namespace EasyPost\Service;

use EasyPost\Util\InternalUtil;

/**
 * CustomsInfo service containing all the logic to make API calls.
 */
class CustomsInfoService extends BaseService
{
    /**
     * Retrieve a customs info.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve(string $id): mixed
    {
        return self::retrieveResource(self::serviceModelClassName(self::class), $id);
    }

    /**
     * Create a customs info.
     *
     * @param mixed $params
     * @return mixed
     */
    public function create(mixed $params = null): mixed
    {
        $params = InternalUtil::wrapParams($params, 'customs_info');

        return self::createResource(self::serviceModelClassName(self::class), $params);
    }
}

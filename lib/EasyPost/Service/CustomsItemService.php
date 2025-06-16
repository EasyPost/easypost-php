<?php

namespace EasyPost\Service;

use EasyPost\Util\InternalUtil;

/**
 * CustomsItem service containing all the logic to make API calls.
 */
class CustomsItemService extends BaseService
{
    /**
     * Retrieve a customs item.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve(string $id): mixed
    {
        return self::retrieveResource(self::serviceModelClassName(self::class), $id);
    }

    /**
     * Create a customs item.
     *
     * @param mixed $params
     * @return mixed
     */
    public function create(mixed $params = null): mixed
    {
        $params = InternalUtil::wrapParams($params, 'customs_item');

        return self::createResource(self::serviceModelClassName(self::class), $params);
    }
}

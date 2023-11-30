<?php

namespace EasyPost\Service;

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
        if (!isset($params['customs_item']) || !is_array($params['customs_item'])) {
            $clone = $params;
            unset($params);
            $params['customs_item'] = $clone;
        }

        return self::createResource(self::serviceModelClassName(self::class), $params);
    }
}

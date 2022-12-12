<?php

namespace EasyPost\Service;

/**
 * CustomsItem service containing all the logic to make API calls.
 */
class CustomsItemService extends BaseService
{
    private static $modelClass = 'CustomsItem';

    /**
     * Retrieve a customs item.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve($id)
    {
        return self::retrieveResource(self::$modelClass, $id);
    }

    /**
     * Create a customs item.
     *
     * @param mixed $params
     * @return mixed
     */
    public function create($params = null)
    {
        if (!isset($params['customs_item']) || !is_array($params['customs_item'])) {
            $clone = $params;
            unset($params);
            $params['customs_item'] = $clone;
        }

        return self::createResource(self::$modelClass, $params);
    }
}

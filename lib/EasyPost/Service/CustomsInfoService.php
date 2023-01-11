<?php

namespace EasyPost\Service;

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
    public function retrieve($id)
    {
        return self::retrieveResource(self::serviceModelClassName(self::class), $id);
    }

    /**
     * Create a customs info.
     *
     * @param mixed $params
     * @return mixed
     */
    public function create($params = null)
    {
        if (!isset($params['customs_info']) || !is_array($params['customs_info'])) {
            $clone = $params;
            unset($params);
            $params['customs_info'] = $clone;
        }

        return self::createResource(self::serviceModelClassName(self::class), $params);
    }
}

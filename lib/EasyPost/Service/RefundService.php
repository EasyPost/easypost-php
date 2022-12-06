<?php

namespace EasyPost\Service;

/**
 * Refund service containing all the logic to make API calls.
 */
class RefundService extends BaseService
{
    private static $modelClass = 'Refund';

    /**
     * Retrieve a refund.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve($id)
    {
        return self::retrieveResource(self::$modelClass, $id);
    }

    /**
     * Retrieve all refunds.
     *
     * @param mixed $params
     * @return mixed
     */
    public function all($params = null)
    {
        return self::allResources(self::$modelClass, $params);
    }

    /**
     * Create a refund.
     *
     * @param mixed $params
     * @return mixed
     */
    public function create($params = null)
    {
        if (!isset($params['refund']) || !is_array($params['refund'])) {
            $clone = $params;
            unset($params);
            $params['refund'] = $clone;
        }

        return self::createResource(self::$modelClass, $params);
    }
}

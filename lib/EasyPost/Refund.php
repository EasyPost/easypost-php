<?php

namespace EasyPost;

class Refund extends EasypostResource
{
    /**
     * Retrieve a refund.
     *
     * @param string $id
     * @param string $apiKey
     * @return mixed
     */
    public static function retrieve($id, $apiKey = null)
    {
        return self::retrieveResource(get_class(), $id, $apiKey);
    }

    /**
     * Retrieve all refunds.
     *
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     */
    public static function all($params = null, $apiKey = null)
    {
        return self::allResources(get_class(), $params, $apiKey);
    }

    /**
     * Create a refund.
     *
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        if (!isset($params['refund']) || !is_array($params['refund'])) {
            $clone = $params;
            unset($params);
            $params['refund'] = $clone;
        }

        return self::createResource(get_class(), $params, $apiKey);
    }
}

<?php

namespace EasyPost;

class Refund extends Resource
{
    public static function retrieve($id, $apiKey = null)
    {
        return self::_retrieve(get_class(), $id, $apiKey);
    }

    public static function all($params = null, $apiKey = null)
    {
        return self::_all(get_class(), $params, $apiKey);
    }

    public static function create($params = null, $apiKey = null)
    {
        if (!isset($params['refund']) || !is_array($params['refund'])) {
            $clone = $params;
            unset($params);
            $params['refund'] = $clone;
        }

        return self::_create(get_class(), $params, $apiKey);
    }
}


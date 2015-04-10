<?php

namespace EasyPost;

class Container extends Resource
{
    public static function retrieve($id, $apiKey = null)
    {
        return self::_retrieve(get_class(), $id, $apiKey);
    }

    public static function all($params = null, $apiKey = null)
    {
        return self::_all(get_class(), $params, $apiKey);
    }

    public function save()
    {
        return self::_save(get_class());
    }

    public static function create($params = null, $apiKey = null)
    {
        if (!isset($params['container']) || !is_array($params['container'])) {
            $clone = $params;
            unset($params);
            $params['container'] = $clone;
        }

        return self::_create(get_class(), $params, $apiKey);
    }
}

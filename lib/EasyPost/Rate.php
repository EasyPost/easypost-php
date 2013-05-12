<?php

namespace EasyPost;

class Rate extends Resource
{
    public static function constructFrom($values, $class = null, $apiKey = null)
    {
        $class = get_class();

        return self::constructFrom($values, $class, $apiKey);
    }

    public static function retrieve($id, $apiKey = null)
    {
        $class = get_class();

        return self::_retrieve($class, $id, $apiKey);
    }

    public static function all($params = null, $apiKey = null)
    {
        $class = get_class();

        return self::_all($class, $params, $apiKey);
    }

    public static function create($params = null, $apiKey = null)
    {
        $class = get_class();
        if (!isset($params['rate']) || !is_array($params['rate'])) {
            $clone = $params;
            unset($params);
            $params['rate'] = $clone;
        }
        if (isset($params['rate']['id']) && strpos($params['rate']['id'], "shp") !== -1) {
            $clone = $params;
            unset($params);
            $params['rate']['shipment'] = $clone['rate'];
        }

        return self::_create($class, $params, $apiKey);
    }

    public function save()
    {
        $class = get_class();

        return self::_save($class);
    }
}

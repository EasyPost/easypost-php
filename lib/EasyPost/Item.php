<?php

namespace EasyPost;

class Item extends Resource
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
        if (!isset($params['item']) || !is_array($params['item'])) {
            $clone = $params;
            unset($params);
            $params['item'] = $clone;
        }

        return self::_create($class, $params, $apiKey);
    }

    public function save()
    {
        $class = get_class();

        return self::_save($class);
    }

    public static function retrieve_reference($params = null, $apiKey = null)
    {
        $class = get_class();

        $requestor = new Requestor($apiKey);
        $url = self::classUrl($class);
        list($response, $apiKey) = $requestor->request('get', $url.'/retrieve_reference', $params);

        return Util::convertToEasyPostObject($response, $apiKey);
    }
}

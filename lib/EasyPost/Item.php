<?php

namespace EasyPost;

class Item extends Resource
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
        if (!isset($params['item']) || !is_array($params['item'])) {
            $clone = $params;
            unset($params);
            $params['item'] = $clone;
        }

        return self::_create(get_class(), $params, $apiKey);
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

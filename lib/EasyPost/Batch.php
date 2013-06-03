<?php

namespace EasyPost;

class Batch extends Resource
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
        if (!isset($params['batch']) || !is_array($params['batch'])) {
            $clone = $params;
            unset($params);
            $params['batch'] = $clone;
        }

        return self::_create($class, $params, $apiKey);
    }

    public static function create_and_buy($params = null, $apiKey = null)
    {
        $class = get_class();
        if (!isset($params['batch']) || !is_array($params['batch'])) {
            $clone = $params;
            unset($params);
            $params['batch'] = $clone;
        }

        $requestor = new Requestor($apiKey);
        $url = self::classUrl($class);
        list($response, $apiKey) = $requestor->request('post', $url.'/create_and_buy', $params);

        return Util::convertToEasyPostObject($response, $apiKey);
    }

    public function label($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/label';

        list($response, $apiKey) = $requestor->request('post', $url, $params);
        $this->refreshFrom($response, $apiKey, true);

        return $this;
    }
}

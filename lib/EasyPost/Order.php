<?php

namespace EasyPost;

class Order extends Resource
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
        if (!isset($params['order']) || !is_array($params['order'])) {
            $clone = $params;
            unset($params);
            $params['order'] = $clone;
        }

        return self::_create($class, $params, $apiKey);
    }

    public function save()
    {
        $class = get_class();

        return self::_save($class);
    }

    public function buy($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/buy';

        if ($params instanceof Rate) {
            $clone = $params;
            unset($params);
            $params['carrier'] = $clone->carrier;
            $params['service'] = $clone->service;
        }

        list($response, $apiKey) = $requestor->request('post', $url, $params);
        $this->refreshFrom($response, $apiKey, false);

        return $this;
    }
}

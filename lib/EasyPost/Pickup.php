<?php

namespace EasyPost;

class Pickup extends Resource
{
    public static function retrieve($id, $apiKey = null)
    {
        return self::_retrieve(get_class(), $id, $apiKey);
    }

    public static function create($params = null, $apiKey = null)
    {
        if (!isset($params['pickup']) || !is_array($params['pickup'])) {
            $clone = $params;
            unset($params);
            $params['pickup'] = $clone;
        }

        return self::_create(get_class(), $params, $apiKey);
    }

    public function buy($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/buy';

        list($response, $apiKey) = $requestor->request('post', $url, $params);
        $this->refreshFrom($response, $apiKey, true);

        return $this;
    }

    public function cancel($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/cancel';

        list($response, $apiKey) = $requestor->request('post', $url, $params);
        $this->refreshFrom($response, $apiKey, true);

        return $this;
    }
}

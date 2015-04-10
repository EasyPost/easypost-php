<?php

namespace EasyPost;

class Address extends Resource
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
        if (!isset($params['address']) || !is_array($params['address'])) {
            $clone = $params;
            unset($params);
            $params['address'] = $clone;
        }

        return self::_create(get_class(), $params, $apiKey);
    }

    public static function create_and_verify($params = null, $apiKey = null)
    {
        $class = get_class();
        if (!isset($params['address']) || !is_array($params['address'])) {
            $clone = $params;
            unset($params);
            $params['address'] = $clone;
        }

        $requestor = new Requestor($apiKey);
        $url = self::classUrl($class);
        list($response, $apiKey) = $requestor->request('post', $url.'/create_and_verify', $params);

        if (isset($response['address'])) {
            $verified_address = Util::convertToEasyPostObject($response['address'], $apiKey);
            if (!empty($response['message'])) {
                $verified_address->message = $response['message'];
                $verified_address->_immutableValues[] = 'message';
            }

            return $verified_address;
        } else {

            return Util::convertToEasyPostObject($response, $apiKey);
        }
    }

    public function verify($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/verify';
        list($response, $apiKey) = $requestor->request('get', $url, $params);
        if (isset($response['address'])) {
            $verified_address = Util::convertToEasyPostObject($response['address'], $apiKey);
            if (!empty($response['message'])) {
                $verified_address->message = $response['message'];
                $verified_address->_immutableValues[] = 'message';
            }

            return $verified_address;
        } else {

            return Util::convertToEasyPostObject($response, $apiKey);
        }
    }
}

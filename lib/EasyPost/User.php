<?php

namespace EasyPost;

class User extends Resource
{
    public static function retrieve($id, $apiKey = null)
    {
        return self::_retrieve(get_class(), $id, $apiKey);
    }

    public function save()
    {
        return self::_save(get_class());
    }

    public static function create($params = null, $apiKey = null)
    {
        if (!isset($params['user']) || !is_array($params['user'])) {
            $clone = $params;
            unset($params);
            $params['user'] = $clone;
        }
        return self::_create(get_class(), $params, $apiKey);
    }

    public static function retrieve_me($apiKey = null)
    {
        return self::_all(get_class(), null, $apiKey);
    }

    public static function all_api_keys($apiKey = null)
    {
        $requestor = new Requestor($apiKey);
        list($response, $apiKey) = $requestor->request('get', '/api_keys');
        return Util::convertToEasyPostObject($response, $apiKey);
    }

    public function api_keys($apiKey = null)
    {
        $api_keys = self::all_api_keys();
        $my_api_keys = null;

        if ($api_keys->id == $this->id) {
            $my_api_keys = $api_keys->keys;
        }
        if (is_null($my_api_keys)) {
            foreach($api_keys->children as $children_keys) {
                if ($children_keys->id == $this->id) {
                    $my_api_keys = $children_keys->keys;
                }
            }
        }

        if (is_null($my_api_keys)) {
            return null;
        } else {
            $response = array();
            foreach($my_api_keys as $key) {
                $response[$key->mode] = $key->key;
            }
            return $response;
        }
    }
}


<?php

namespace EasyPost;

class User extends EasypostResource
{
    /**
     * retrieve a user
     *
     * @param string $id
     * @param string $apiKey
     * @return mixed
     */
    public static function retrieve($id, $apiKey = null)
    {
        return self::_retrieve(get_class(), $id, $apiKey);
    }

    /**
     * save a user
     *
     * @return $this
     */
    public function save()
    {
        return self::_save(get_class());
    }

    /**
     * create a user
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        if (!isset($params['user']) || !is_array($params['user'])) {
            $clone = $params;
            unset($params);
            $params['user'] = $clone;
        }
        return self::_create(get_class(), $params, $apiKey, null, false);
    }

    /**
     * retrieve me
     *
     * @param string $apiKey
     * @return mixed
     */
    public static function retrieve_me($apiKey = null)
    {
        return self::_all(get_class(), null, $apiKey);
    }

    /**
     * get all API keys
     *
     * @param null $apiKey
     * @return mixed
     */
    public static function all_api_keys($apiKey = null)
    {
        $requestor = new Requestor($apiKey);
        list($response, $apiKey) = $requestor->request('get', '/api_keys');
        return Util::convertToEasyPostObject($response, $apiKey);
    }

    /**
     * api keys
     *
     * @param string $apiKey
     * @return array|null
     */
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


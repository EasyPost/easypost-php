<?php

namespace EasyPost;

class Tracker extends EasypostResource
{
    /**
     * retrieve a tracker
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
     * retrieve all trackers
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return mixed
     */
    public static function all($params = null, $apiKey = null)
    {
        return self::_all(get_class(), $params, $apiKey);
    }

    /**
     * save a tracker
     *
     * @return $this
     */
    public function save()
    {
        return self::_save(get_class());
    }

    /**
     * create a tracker
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        if (!is_array($params)) {
          $clone = $params;
          unset($params);
          $params['tracker']['tracking_code'] = $clone;
        } else if (!isset($params['tracker']) || !is_array($params['tracker'])) {
            $clone = $params;
            unset($params);
            $params['tracker'] = $clone;
        }

        return self::_create(get_class(), $params, $apiKey);
    }

    /**
     * create a list of trackers
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create_list($params = null, $apiKey = null)
    {
        $class = get_class();
        if (!isset($params['trackers']) || !is_array($params['trackers'])) {
            $clone = $params;
            unset($params);
            $params['trackers'] = $clone;
        }

        $requestor = new Requestor($apiKey);
        $url = self::classUrl($class);
        list($response, $apiKey) = $requestor->request('post', $url.'/create_list', $params);
    }
}


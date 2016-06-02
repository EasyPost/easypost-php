<?php

namespace EasyPost;

class CustomsInfo extends EasypostResource
{
    /**
     * retrieve a customs info
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
     * retrieve all customs info
     *
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     */
    public static function all($params = null, $apiKey = null)
    {
        return self::_all(get_class(), $params, $apiKey);
    }

    /**
     * save a customs info
     *
     * @return $this
     */
    public function save()
    {
        return self::_save(get_class());
    }

    /**
     * create a customs info
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        if (!isset($params['customs_info']) || !is_array($params['customs_info'])) {
            $clone = $params;
            unset($params);
            $params['customs_info'] = $clone;
        }

        return self::_create(get_class(), $params, $apiKey);
    }
}

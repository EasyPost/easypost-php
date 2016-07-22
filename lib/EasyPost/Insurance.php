<?php

namespace EasyPost;

class Insurance extends EasypostResource
{
    /**
     * retrieve an insurance
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
     * save an insurance
     *
     * @return $this
     */
    public function save()
    {
        return self::_save(get_class());
    }

    /**
     * retrieve all insurances
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
     * create an insurance
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        if (!isset($params['insurance']) || !is_array($params['insurance'])) {
            $clone = $params;
            unset($params);
            $params['insurance'] = $clone;
        }

        return self::_create(get_class(), $params, $apiKey);
    }

}


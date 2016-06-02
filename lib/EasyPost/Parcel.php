<?php

namespace EasyPost;

class Parcel extends EasypostResource
{
    /**
     * retrieve a parcel
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
     * retrieve all parcels
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
     * save a parcel
     *
     * @return $this
     */
    public function save()
    {
        return self::_save(get_class());
    }

    /**
     * create a parcel
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        if (!isset($params['parcel']) || !is_array($params['parcel'])) {
            $clone = $params;
            unset($params);
            $params['parcel'] = $clone;
        }

        return self::_create(get_class(), $params, $apiKey);
    }
}

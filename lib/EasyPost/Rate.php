<?php

namespace EasyPost;

class Rate extends EasypostResource
{
    /**
     * retrieve a rate
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
     * retrieve all rates
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
     * save a rate
     *
     * @return $this
     */
    public function save()
    {
        return self::_save(get_class());
    }

    /**
     * create a rate
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        if (!isset($params['rate']) || !is_array($params['rate'])) {
            $clone = $params;
            unset($params);
            $params['rate'] = $clone;
        }
        if (isset($params['rate']['id']) && strpos($params['rate']['id'], "shp") !== -1) {
            $clone = $params;
            unset($params);
            $params['rate']['shipment'] = $clone['rate'];
        }

        return self::_create(get_class(), $params, $apiKey);
    }
}

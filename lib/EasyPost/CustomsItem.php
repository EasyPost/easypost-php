<?php

namespace EasyPost;

class CustomsItem extends EasypostResource
{
    /**
     * retrieve a customs item
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
     * retrieve all customs items
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
     * save the customs item
     *
     * @return $this
     */
    public function save()
    {
        return self::_save(get_class());
    }

    /**
     * create a customs item
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        if (!isset($params['customs_item']) || !is_array($params['customs_item'])) {
            $clone = $params;
            unset($params);
            $params['customs_item'] = $clone;
        }

        return self::_create(get_class(), $params, $apiKey);
    }
}

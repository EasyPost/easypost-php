<?php

namespace EasyPost;

class Item extends EasypostResource
{
    /**
     * retrieve an item
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
     * retrieve all items
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
     * save an item
     *
     * @return $this
     */
    public function save()
    {
        return self::_save(get_class());
    }

    /**
     * create an item
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        if (!isset($params['item']) || !is_array($params['item'])) {
            $clone = $params;
            unset($params);
            $params['item'] = $clone;
        }

        return self::_create(get_class(), $params, $apiKey);
    }

    /**
     * retrieve item reference
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return array
     */
    public static function retrieve_reference($params = null, $apiKey = null)
    {
        $class = get_class();
        $requestor = new Requestor($apiKey);
        $url = self::classUrl($class);
        list($response, $apiKey) = $requestor->request('get', $url.'/retrieve_reference', $params);

        return Util::convertToEasyPostObject($response, $apiKey);
    }
}

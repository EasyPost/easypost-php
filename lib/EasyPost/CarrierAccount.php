<?php

namespace EasyPost;

class CarrierAccount extends EasypostResource
{
    /**
     * retrieve a carrier account
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
     * retrieve all carrier accounts
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
     * save carrier account
     *
     * @return $this
     */
    public function save()
    {
        return self::_save(get_class());
    }

    /**
     * delete carrier account
     *
     * @return $this
     */
    public function delete()
    {
        return self::_delete(get_class());
    }

    /**
     * create carrier account
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        if (!isset($params['carrier_account']) || !is_array($params['carrier_account'])) {
            $clone = $params;
            unset($params);
            $params['carrier_account'] = $clone;
        }
        return self::_create(get_class(), $params, $apiKey);
    }

    /**
     * get types of carrier account
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return mixed
     */
    public static function types($params = null, $apiKey = null)
    {
        $requestor = new Requestor($apiKey);
        list($response, $apiKey) = $requestor->request('get', '/carrier_types', $params);
        return Util::convertToEasyPostObject($response, $apiKey);
    }
}


<?php

namespace EasyPost;

class Batch extends EasypostResource
{
    /**
     * retrieve a batch
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
     * retrieve all batches
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
     * create a batch
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        if (!isset($params['batch']) || !is_array($params['batch'])) {
            $clone = $params;
            unset($params);
            $params['batch'] = $clone;
        }

        return self::_create(get_class(), $params, $apiKey);
    }

    /**
     * create and buy a batch
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create_and_buy($params = null, $apiKey = null)
    {
        $class = get_class();
        if (!isset($params['batch']) || !is_array($params['batch'])) {
            $clone = $params;
            unset($params);
            $params['batch'] = $clone;
        }

        $requestor = new Requestor($apiKey);
        $url = self::classUrl($class);
        list($response, $apiKey) = $requestor->request('post', $url.'/create_and_buy', $params);

        return Util::convertToEasyPostObject($response, $apiKey);
    }

    /**
     * buy a batch
     *
     * @param mixed $params
     * @return $this
     * @throws \EasyPost\Error
     */
    public function buy($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/buy';

        list($response, $apiKey) = $requestor->request('post', $url, $params);
        $this->refreshFrom($response, $apiKey, true);

        return $this;
    }

    /**
     * @param mixed $params
     * @return $this
     * @throws \EasyPost\Error
     */
    public function label($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/label';

        list($response, $apiKey) = $requestor->request('post', $url, $params);
        $this->refreshFrom($response, $apiKey, true);

        return $this;
    }

    /**
     * remove shipments from a batch
     *
     * @param mixed $params
     * @return $this
     * @throws \EasyPost\Error
     */
    public function remove_shipments($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/remove_shipments';

        list($response, $apiKey) = $requestor->request('post', $url, $params);
        $this->refreshFrom($response, $apiKey, true);

        return $this;
    }

    /**
     * add shipments to a batch
     *
     * @param mixed $params
     * @return $this
     * @throws \EasyPost\Error
     */
    public function add_shipments($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/add_shipments';

        list($response, $apiKey) = $requestor->request('post', $url, $params);
        $this->refreshFrom($response, $apiKey, true);

        return $this;
    }

    /**
     * create a batch scan form
     *
     * @param mixed $params
     * @return mixed
     * @throws \EasyPost\Error
     */
    public function create_scan_form($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/scan_form';

        list($response, $apiKey) = $requestor->request('post', $url, $params);

        return Util::convertToEasyPostObject($response, $apiKey);
    }
}

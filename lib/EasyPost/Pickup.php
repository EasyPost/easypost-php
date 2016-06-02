<?php

namespace EasyPost;

class Pickup extends EasypostResource
{
    /**
     * retrieve a pickup
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
     * create a pickup
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        if (!isset($params['pickup']) || !is_array($params['pickup'])) {
            $clone = $params;
            unset($params);
            $params['pickup'] = $clone;
        }

        return self::_create(get_class(), $params, $apiKey);
    }

    /**
     * buy a pickup
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
     * cancel a pickup
     *
     * @param mixed $params
     * @return $this
     * @throws \EasyPost\Error
     */
    public function cancel($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/cancel';

        list($response, $apiKey) = $requestor->request('post', $url, $params);
        $this->refreshFrom($response, $apiKey, true);

        return $this;
    }
}

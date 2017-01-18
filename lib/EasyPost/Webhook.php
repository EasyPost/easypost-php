<?php

namespace EasyPost;

class Webhook extends EasypostResource
{
    /**
     * retrieve a webhook
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
     * retrieve all webhooks
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
     * delete a webhook
     *
     * @param string $apiKey
     * @return $this
     */
    public function delete($params = null, $apiKey = null)
    {
        return self::_delete(get_class(), $params, true);
    }

    /**
     * update a webhook
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return $this
     */
    public function update($params = null, $apiKey = null)
    {
        if (!isset($params['webhook']) || !is_array($params['webhook'])) {
            $clone = $params;
            unset($params);
            $params['webhook'] = $clone;
        }

        return self::_update(get_class(), $params);
    }

    /**
     * create a webhook
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        if (!isset($params['webhook']) || !is_array($params['webhook'])) {
            $clone = $params;
            unset($params);
            $params['webhook'] = $clone;
        }

        return self::_create(get_class(), $params, $apiKey);
    }
}

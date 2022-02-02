<?php

namespace EasyPost;

/**
 * @package EasyPost
 * @property string $id
 * @property string $object
 * @property string $modoe
 * @property string $url
 * @property string $disabled_at
 */
class Webhook extends EasypostResource
{
    /**
     * Retrieve a webhook.
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
     * Retrieve all webhooks.
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
     * Delete a webhook.
     *
     * @param string $apiKey
     * @return $this
     */
    public function delete($params = null, $apiKey = null)
    {
        return $this->_delete($params, true);
    }

    /**
     * Update a webhook.
     *
     * @param mixed $params
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

        return $this->_update($params);
    }

    /**
     * Create a webhook.
     *
     * @param mixed $params
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

<?php

namespace EasyPost;

class Event extends EasypostResource
{
    /**
     * retrieve an event
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
     * retrieve all events
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
     * receive an event
     *
     * @param string $rawInput
     * @return mixed
     * @throws \EasyPost\Error
     */
    public static function receive($rawInput = null)
    {
        if ($rawInput == null) {
            throw new Error('The raw input must be set');
        }
        $values =  json_decode($rawInput, true);
        if ($values != null) {
            return self::constructFrom($values, get_class(), null);
        } else {
            throw new Error('There was a problem decoding the webhook');
        }
    }
}

<?php

namespace EasyPost;

/**
 * @package EasyPost
 * @property string $id
 * @property string $object
 * @property string $mode
 * @property string $description
 * @property object $previous_attributes
 * @property object $result
 * @property string $status
 * @property array $pending_urls
 * @property array $completed_urls
 * @property string $created_at
 * @property string $updated_at
 */
class Event extends EasypostResource
{
    /**
     * Retrieve an event.
     *
     * @param string $id
     * @param string $apiKey
     * @return mixed
     */
    public static function retrieve($id, $apiKey = null)
    {
        return self::retrieveResource(get_class(), $id, $apiKey);
    }

    /**
     * Retrieve all events.
     *
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     */
    public static function all($params = null, $apiKey = null)
    {
        return self::allResources(get_class(), $params, $apiKey);
    }

    /**
     * Receive an event (convert JSON string to object).
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

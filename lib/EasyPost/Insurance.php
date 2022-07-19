<?php

namespace EasyPost;

/**
 * @package EasyPost
 * @property string $id
 * @property string $object
 * @property string $mode
 * @property string $reference
 * @property string $amount
 * @property string $provider
 * @property string $provider_id
 * @property string $shipment_id
 * @property string $tracking_code
 * @property string $status
 * @property Tracker $tracker
 * @property Address $to_address
 * @property Address $from_address
 * @property Fee $fee
 * @property array $messages
 * @property string $created_at
 * @property string $updated_at
 */
class Insurance extends EasypostResource
{
    /**
     * Retrieve an insurance object.
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
     * Retrieve all insurance objects.
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return mixed
     */
    public static function all($params = null, $apiKey = null)
    {
        return self::allResources(get_class(), $params, $apiKey);
    }

    /**
     * Create an insurance object.
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        if (!isset($params['insurance']) || !is_array($params['insurance'])) {
            $clone = $params;
            unset($params);
            $params['insurance'] = $clone;
        }

        return self::createResource(get_class(), $params, $apiKey);
    }
}

<?php

namespace EasyPost;

/**
 * @package EasyPost
 * @property string $id
 * @property string $object
 * @property string $mode
 * @property float $length
 * @property float $width
 * @property float $height
 * @property float $wiehgt
 * @property string $predefined_package
 * @property string $created_at
 * @property string $updated_at
 */
class Parcel extends EasypostResource
{
    /**
     * Retrieve a parcel.
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
     * Create a parcel.
     *
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        if (!isset($params['parcel']) || !is_array($params['parcel'])) {
            $clone = $params;
            unset($params);
            $params['parcel'] = $clone;
        }

        return self::createResource(get_class(), $params, $apiKey);
    }
}

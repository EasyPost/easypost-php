<?php

namespace EasyPost;

/**
 * @package EasyPost
 * @property string $id
 * @property string $object
 * @property string $description
 * @property string $quantity
 * @property float $value
 * @property float $weight
 * @property string $hs_tariff_number
 * @property string $code
 * @property string $origin_country
 * @property string $currency
 * @property string $created_at
 * @property string $updated_at
 */
class CustomsItem extends EasypostResource
{
    /**
     * Retrieve a customs item.
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
     * Create a customs item.
     *
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        if (!isset($params['customs_item']) || !is_array($params['customs_item'])) {
            $clone = $params;
            unset($params);
            $params['customs_item'] = $clone;
        }

        return self::createResource(get_class(), $params, $apiKey);
    }
}

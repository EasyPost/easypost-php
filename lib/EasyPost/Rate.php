<?php

namespace EasyPost;

/**
 * @package EasyPost
 * @property string $id
 * @property string $object
 * @property string $mode
 * @property string $service
 * @property string $carrier
 * @property string $carrier_account_id
 * @property string $shipment_id
 * @property string $rate
 * @property string $currency
 * @property string $retail_rate
 * @property string $retail_currency
 * @property string $list_rate
 * @property string $list_currency
 * @property int $delivery_days
 * @property string $delivery_date
 * @property bool $delivery_date_guaranteed
 * @property CarbonOffset $carbon_offset
 * @property string $created_at
 * @property string $updated_at
 */
class Rate extends EasypostResource
{
    /**
     * Retrieve a rate.
     *
     * @param string $id
     * @param string $apiKey
     * @return mixed
     */
    public static function retrieve($id, $apiKey = null)
    {
        return self::retrieveResource(get_class(), $id, $apiKey);
    }
}

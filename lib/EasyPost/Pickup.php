<?php

namespace EasyPost;

use EasyPost\Util\InternalUtil;

/**
 * @package EasyPost
 * @property string $id
 * @property string $object
 * @property string $reference
 * @property string $mode
 * @property string $status
 * @property string $min_datetime
 * @property string $max_datetime
 * @property bool $is_account_address
 * @property string $instructions
 * @property Message[] $messages
 * @property string $confirmation
 * @property Shipment $shipment
 * @property Address $address
 * @property CarrierAccount[] $carrier_accounts
 * @property PickupRate[] $pickup_rates
 * @property string $created_at
 * @property string $updated_at
 */
class Pickup extends EasyPostObject
{
    /**
     * Get the lowest rate for the pickup.
     *
     * To exclude a carrier or service, prepend the string with `!`.
     *
     * @param array $carriers
     * @param array $services
     * @return Rate
     */
    public function lowestRate($carriers = [], $services = [])
    {
        $lowestRate = InternalUtil::getLowestObjectRate($this, $carriers, $services, 'pickup_rates');

        return $lowestRate;
    }
}

<?php

namespace EasyPost;

use EasyPost\PickupRate;
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
     * @param array<string>|null $carriers
     * @param array<string>|null $services
     * @return PickupRate
     */
    public function lowestRate(?array $carriers = [], ?array $services = []): PickupRate
    {
        /** @var PickupRate $lowestRate */
        $lowestRate = InternalUtil::getLowestObjectRate($this, $carriers, $services, 'pickup_rates');

        return $lowestRate;
    }
}

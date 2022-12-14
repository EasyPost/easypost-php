<?php

namespace EasyPost;

use EasyPost\Util\InternalUtil;

/**
 * @package EasyPost
 * @property string $id
 * @property string $object
 * @property string $reference
 * @property string $mode
 * @property Address $to_address
 * @property Address $from_address
 * @property Address $return_address
 * @property Address $buyer_address
 * @property Shipment[] $shipments
 * @property Rates[] $rates
 * @property Message[] $messages
 * @property bool $is_return
 * @property string $created_at
 * @property string $updated_at
 */
class Order extends EasyPostObject
{
    /**
     * Get the lowest rate for the order.
     *
     * To exclude a carrier or service, prepend the string with `!`.
     *
     * @param array $carriers
     * @param array $services
     * @return Rate
     */
    public function lowestRate($carriers = [], $services = [])
    {
        $lowestRate = InternalUtil::getLowestObjectRate($this, $carriers, $services);

        return $lowestRate;
    }
}

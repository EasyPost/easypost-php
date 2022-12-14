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
 * @property Parcel $parcel
 * @property CustomsInfo $customs_info
 * @property ScanForm $scan_form
 * @property array $forms
 * @property Insurance $insurance
 * @property Rate[] $rates
 * @property Rate $selected_rate
 * @property PostageLabel $postage_label
 * @property Message[] $messages
 * @property object $options
 * @property bool $is_return
 * @property string $tracking_code
 * @property bool $usps_zone
 * @property string $status
 * @property Tracker $tracker
 * @property Fee[] $fees
 * @property string $refund_status
 * @property string $batch_id
 * @property string $batch_status
 * @property string $batch_message
 * @property string $created_at
 * @property string $updated_at
 */
class Shipment extends EasyPostObject
{
    /**
     * Get the lowest rate for the shipment.
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

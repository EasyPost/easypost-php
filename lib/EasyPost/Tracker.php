<?php

namespace EasyPost;

/**
 * @package EasyPost
 * @property string $id
 * @property string $object
 * @property string $mode
 * @property string $tracking_code
 * @property string $status
 * @property string $status_detail
 * @property string $signed_by
 * @property float $weight
 * @property string $est_delivery_date
 * @property string $shipment_id
 * @property string $carrier
 * @property TrackingDetail[] $tracking_details
 * @property CarrierDetail $carrier_detail
 * @property string $public_url
 * @property Fee[] $fees
 * @property string $created_at
 * @property string $updated_at
 */
class Tracker extends EasyPostObject
{
}

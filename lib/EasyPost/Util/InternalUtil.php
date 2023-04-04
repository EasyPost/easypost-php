<?php

namespace EasyPost\Util;

use EasyPost\Address;
use EasyPost\ApiKey;
use EasyPost\Batch;
use EasyPost\Brand;
use EasyPost\CarrierAccount;
use EasyPost\CarrierDetail;
use EasyPost\Constant\Constants;
use EasyPost\CustomsInfo;
use EasyPost\CustomsItem;
use EasyPost\EasyPostObject;
use EasyPost\EndShipper;
use EasyPost\Event;
use EasyPost\Exception\General\FilteringException;
use EasyPost\Fee;
use EasyPost\Insurance;
use EasyPost\Order;
use EasyPost\Parcel;
use EasyPost\Pickup;
use EasyPost\PickupRate;
use EasyPost\PostageLabel;
use EasyPost\Rate;
use EasyPost\Refund;
use EasyPost\Report;
use EasyPost\ScanForm;
use EasyPost\Shipment;
use EasyPost\Tracker;
use EasyPost\TrackingDetail;
use EasyPost\TrackingLocation;
use EasyPost\User;
use EasyPost\Webhook;

const OBJECT_MAPPING = [
    'Address'               => Address::class,
    'ApiKey'                => ApiKey::class,
    'Batch'                 => Batch::class,
    'Brand'                 => Brand::class,
    'CarrierAccount'        => CarrierAccount::class,
    'CarrierDetail'         => CarrierDetail::class,
    'CustomsInfo'           => CustomsInfo::class,
    'CustomsItem'           => CustomsItem::class,
    'EndShipper'            => EndShipper::class,
    'Event'                 => Event::class,
    'Fee'                   => Fee::class,
    'Insurance'             => Insurance::class,
    'Order'                 => Order::class,
    'Parcel'                => Parcel::class,
    'PaymentLogReport'      => Report::class,
    'Pickup'                => Pickup::class,
    'PickupRate'            => PickupRate::class,
    'PostageLabel'          => PostageLabel::class,
    'Rate'                  => Rate::class,
    'Refund'                => Refund::class,
    'RefundReport'          => Report::class,
    'Report'                => Report::class,
    'ScanForm'              => ScanForm::class,
    'Shipment'              => Shipment::class,
    'ShipmentInvoiceReport' => Report::class,
    'ShipmentReport'        => Report::class,
    'TaxIdentifier'         => TaxIdentifier::class,
    'Tracker'               => Tracker::class,
    'TrackerReport'         => Report::class,
    'TrackingDetail'        => TrackingDetail::class,
    'TrackingLocation'      => TrackingLocation::class,
    'User'                  => User::class,
    'Webhook'               => Webhook::class,
];

const OBJECT_ID_PREFIXES = [
    'adr'       => Address::class,
    'ak'        => ApiKey::class,
    'batch'     => Batch::class,
    'brd'       => Brand::class,
    'ca'        => CarrierAccount::class,
    'cstinfo'   => CustomsInfo::class,
    'cstitem'   => CustomsItem::class,
    'es'        => EndShipper::class,
    'evt'       => Event::class,
    'fee'       => Fee::class,
    'hook'      => Webhook::class,
    'ins'       => Insurance::class,
    'order'     => Order::class,
    'pickup'    => Pickup::class,
    'pl'        => PostageLabel::class,
    'plrep'     => Report::class,
    'prcl'      => Parcel::class,
    'rate'      => Rate::class,
    'refrep'    => Report::class,
    'rfnd'      => Refund::class,
    'sf'        => ScanForm::class,
    'shp'       => Shipment::class,
    'shpinvrep' => Report::class,
    'shprep'    => Report::class,
    'trk'       => Tracker::class,
    'trkrep'    => Report::class,
    'user'      => User::class,
];

abstract class InternalUtil
{
    /**
     * Check if input is a list (eg: sequential array).
     *
     * PHP treats JSON objects (associative arrays) and lists (sequential arrays) as the
     * same thing (array), so one can use this function to determine what kind of array something is.
     *
     * @param $array
     * @return bool
     */
    public static function isList($array)
    {
        if (!is_array($array)) {
            return false;
        }

        foreach (array_keys($array) as $key) {
            if (!is_numeric($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Convert input to an EasyPost object.
     *
     * @param EasyPostClient $client
     * @param mixed $response
     * @return mixed
     */
    public static function convertToEasyPostObject($client, $response)
    {
        if (InternalUtil::isList($response)) {
            $mapped = [];
            foreach ($response as $object => $value) {
                if (is_string($object) && isset(OBJECT_MAPPING[$object])) {
                    $value['object'] = $object;
                }
                array_push($mapped, self::convertToEasyPostObject($client, $value));
            }

            return $mapped;
        } elseif (is_array($response)) {
            if (
                isset($response['object'])
                && is_string($response['object'])
                && isset(OBJECT_MAPPING[$response['object']])
            ) {
                $class = OBJECT_MAPPING[$response['object']];
            } elseif (
                isset($response['id'])
                && isset(OBJECT_ID_PREFIXES[substr($response['id'], 0, strpos($response['id'], '_'))])
            ) {
                $class = OBJECT_ID_PREFIXES[substr($response['id'], 0, strpos($response['id'], '_'))];
            } else {
                $class = '\EasyPost\EasyPostObject';
            }

            return EasyPostObject::constructFrom($client, $response, $class);
        } else {
            return $response;
        }
    }

    /**
     * Get the lowest rate of an EasyPost object (eg: Shipment, Order, Pickup).
     * To exclude a carrier or service, prepend the string with `!`.
     *
     * This internal utility is intended to be used by other EasyPost `lowest_rate` functions.
     *
     * @param object EasyPostObject
     * @param array $carriers
     * @param array $services
     * @param string $ratesKey
     * @return Rate
     * @throws \EasyPost\Exception\EasyPostException
     */
    public static function getLowestObjectRate($easypostObject, $carriers = [], $services = [], $ratesKey = 'rates')
    {
        $lowestRate = false;
        $carriersInclude = [];
        $carriersExclude = [];
        $servicesInclude = [];
        $servicesExclude = [];

        if (!is_array($carriers)) {
            $carriers = explode(',', $carriers);
        }
        for ($i = 0; $i < count($carriers); $i++) {
            $carriers[$i] = trim(strtolower($carriers[$i]));
            if (substr($carriers[$i], 0, 1) == '!') {
                $carriersExclude[] = substr($carriers[$i], 1);
            } else {
                $carriersInclude[] = $carriers[$i];
            }
        }

        if (!is_array($services)) {
            $services = explode(',', $services);
        }
        for ($i = 0; $i < count($services); $i++) {
            $services[$i] = trim(strtolower($services[$i]));
            if (substr($services[$i], 0, 1) == '!') {
                $servicesExclude[] = substr($services[$i], 1);
            } else {
                $servicesInclude[] = $services[$i];
            }
        }

        for ($i = 0; $i < count($easypostObject->$ratesKey); $i++) {
            $rateCarrier = strtolower($easypostObject->$ratesKey[$i]->carrier);
            if (!empty($carriersInclude[0]) && !in_array($rateCarrier, $carriersInclude)) {
                continue;
            }
            if (!empty($carriersExclude[0]) && in_array($rateCarrier, $carriersExclude)) {
                continue;
            }

            $rateService = strtolower($easypostObject->$ratesKey[$i]->service);
            if (!empty($servicesInclude[0]) && !in_array($rateService, $servicesInclude)) {
                continue;
            }
            if (!empty($servicesExclude[0]) && in_array($rateService, $servicesExclude)) {
                continue;
            }

            if (!$lowestRate || floatval($easypostObject->$ratesKey[$i]->rate) < floatval($lowestRate->rate)) {
                $lowestRate = clone ($easypostObject->$ratesKey[$i]);
            }
        }

        if ($lowestRate == false) {
            throw new FilteringException(Constants::NO_RATES_ERROR);
        }

        return $lowestRate;
    }
}

<?php

namespace EasyPost\Util;

use EasyPost\EasyPostObject;
use EasyPost\Exception\Error;

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
     * @param string $parent
     * @param string $name
     * @return mixed
     */
    public static function convertToEasyPostObject($client, $response, $parent = null, $name = null)
    {
        $objectMapping = [
            'Address'               => '\EasyPost\Address',
            'ApiKey'                => '\EasyPost\ApiKey',
            'Batch'                 => '\EasyPost\Batch',
            'CarrierAccount'        => '\EasyPost\CarrierAccount',
            'CarrierDetail'         => '\EasyPost\CarrierDetail',
            'CustomsInfo'           => '\EasyPost\CustomsInfo',
            'CustomsItem'           => '\EasyPost\CustomsItem',
            'EndShipper'            => '\EasyPost\EndShipper',
            'Event'                 => '\EasyPost\Event',
            'Fee'                   => '\EasyPost\Fee',
            'Insurance'             => '\EasyPost\Insurance',
            'Message'               => '\EasyPost\Message',
            'Order'                 => '\EasyPost\Order',
            'Parcel'                => '\EasyPost\Parcel',
            'PaymentLogReport'      => '\EasyPost\Report',
            'PaymentMethod'         => '\EasyPost\PaymentMethod',
            'Pickup'                => '\EasyPost\Pickup',
            'PickupRate'            => '\EasyPost\PickupRate',
            'PostageLabel'          => '\EasyPost\PostageLabel',
            'Rate'                  => '\EasyPost\Rate',
            'Refund'                => '\EasyPost\Refund',
            'RefundReport'          => '\EasyPost\Report',
            'Report'                => '\EasyPost\Report',
            'ScanForm'              => '\EasyPost\ScanForm',
            'Shipment'              => '\EasyPost\Shipment',
            'ShipmentInvoiceReport' => '\EasyPost\Report',
            'ShipmentReport'        => '\EasyPost\Report',
            'TaxIdentifier'         => '\EasyPost\TaxIdentifier',
            'Tracker'               => '\EasyPost\Tracker',
            'TrackerReport'         => '\EasyPost\Report',
            'TrackingDetail'        => '\EasyPost\TrackingDetail',
            'TrackingLocation'      => '\EasyPost\TrackingLocation',
            'User'                  => '\EasyPost\User',
            'Verification'          => '\EasyPost\Verification',
            'VerificationDetails'   => '\EasyPost\VerificationDetails',
            'Verifictions'          => '\EasyPost\Verifications',
            'Webhook'               => '\EasyPost\Webhook',
        ];

        $objectIdPrefixes = [
            'adr'       => '\EasyPost\Address',
            'ak'        => '\EasyPost\ApiKey',
            'batch'     => '\EasyPost\Batch',
            'brd'       => '\EasyPost\Brand',
            'ca'        => '\EasyPost\CarrierAccount',
            'cstinfo'   => '\EasyPost\CustomsInfo',
            'cstitem'   => '\EasyPost\CustomsItem',
            'es'        => '\EasyPost\EndShipper',
            'evt'       => '\EasyPost\Event',
            'fee'       => '\EasyPost\Fee',
            'hook'      => '\EasyPost\Webhook',
            'ins'       => '\EasyPost\Insurance',
            'order'     => '\EasyPost\Order',
            'pickup'    => '\EasyPost\Pickup',
            'pl'        => '\EasyPost\PostageLabel',
            'plrep'     => '\EasyPost\Report',
            'prcl'      => '\EasyPost\Parcel',
            'rate'      => '\EasyPost\Rate',
            'refrep'    => '\EasyPost\Report',
            'rfnd'      => '\EasyPost\Refund',
            'sf'        => '\EasyPost\ScanForm',
            'shp'       => '\EasyPost\Shipment',
            'shpinvrep' => '\EasyPost\Report',
            'shprep'    => '\EasyPost\Report',
            'trk'       => '\EasyPost\Tracker',
            'trkrep'    => '\EasyPost\Report',
            'user'      => '\EasyPost\User',
        ];

        if (InternalUtil::isList($response)) {
            $mapped = [];
            foreach ($response as $object => $value) {
                if (is_string($object) && isset($objectMapping[$object])) {
                    $value['object'] = $object;
                }
                array_push($mapped, self::convertToEasyPostObject($client, $value, $parent, $name));
            }

            return $mapped;
        } elseif (is_array($response)) {
            if (isset($response['object']) && is_string($response['object']) && isset($objectMapping[$response['object']])) {
                $class = $objectMapping[$response['object']];
            } elseif (isset($response['id']) && isset($objectIdPrefixes[substr($response['id'], 0, strpos($response['id'], '_'))])) {
                $class = $objectIdPrefixes[substr($response['id'], 0, strpos($response['id'], '_'))];
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
     * @throws \EasyPost\Exception\Error
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
            throw new Error('No rates found.');
        }

        return $lowestRate;
    }
}

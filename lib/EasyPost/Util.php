<?php

namespace EasyPost;

abstract class Util
{
    /**
     * check if input is a list
     *
     * @param $array
     * @return bool
     */
    public static function isList($array)
    {
        if (!is_array($array)) {
            return false;
        }
        foreach (array_keys($array) as $k) {
            if (!is_numeric($k)) {
                return false;
            }
        }

        return true;
    }

    /**
     * convert EasyPost object to an array
     *
     * @param mixed $values
     * @return array
     */
    public static function convertEasyPostObjectToArray($values)
    {
        $results = array();
        foreach ($values as $k => $v) {
            if ($v instanceof EasyPostObject) {
                $results[$k] = $v->__toArray(true);
            } elseif (is_array($v)) {
                $results[$k] = self::convertEasyPostObjectToArray($v);
            } else {
                $results[$k] = $v;
            }
        }

        return $results;
    }

    /**
     * convert input to an EasyPost object
     *
     * @param mixed  $response
     * @param string $apiKey
     * @param string $parent
     * @param string $name
     * @return array
     */
    public static function convertToEasyPostObject($response, $apiKey, $parent = null, $name = null)
    {
        $types = array(
            'Address'               => '\EasyPost\Address',
            'Batch'                 => '\EasyPost\Batch',
            'CarrierAccount'        => '\EasyPost\CarrierAccount',
            'CustomsInfo'           => '\EasyPost\CustomsInfo',
            'CustomsItem'           => '\EasyPost\CustomsItem',
            'Event'                 => '\EasyPost\Event',
            'Fee'                   => '\EasyPost\Fee',
            'Insurance'             => '\EasyPost\Insurance',
            'Order'                 => '\EasyPost\Order',
            'Parcel'                => '\EasyPost\Parcel',
            'PaymentLogReport'      => '\EasyPost\Report',
            'Pickup'                => '\EasyPost\Pickup',
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
            'User'                  => '\EasyPost\User',
            'Webhook'               => '\EasyPost\Webhook',
        );

        $prefixes = array(
            'adr'       => '\EasyPost\Address',
            'batch'     => '\EasyPost\Batch',
            'ca'        => '\EasyPost\CarrierAccount',
            'cstinfo'   => '\EasyPost\CustomsInfo',
            'cstitem'   => '\EasyPost\CustomsItem',
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
        );

        if (self::isList($response)) {
            $mapped = array();
            foreach ($response as $object => $v) {
                if (is_string($object) && isset($types[$object])) {
                    $v['object'] = $object;
                }
                array_push($mapped, self::convertToEasyPostObject($v, $apiKey, $parent, $name));
            }

            return $mapped;
        } elseif (is_array($response)) {
            if (isset($response['object']) && is_string($response['object']) && isset($types[$response['object']])) {
                $class = $types[$response['object']];
            } elseif (isset($response['id']) && isset($prefixes[substr($response['id'], 0, strpos($response['id'], "_"))])) {
                $class = $prefixes[substr($response['id'], 0, strpos($response['id'], "_"))];
            } else {
                $class = '\EasyPost\EasyPostObject';
            }

            return EasyPostObject::constructFrom($response, $class, $apiKey, $parent, $name);
        } else {
            return $response;
        }
    }
}

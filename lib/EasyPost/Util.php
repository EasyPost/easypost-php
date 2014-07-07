<?php

namespace EasyPost;

abstract class Util
{
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

    public static function convertEasyPostObjectToArray($values)
    {
        $results = array();
        foreach ($values as $k => $v) {
            if ($v instanceof Object) {
                $results[$k] = $v->__toArray(true);
            } else if (is_array($v)) {
                $results[$k] = self::convertEasyPostObjectToArray($v);
            } else {
                $results[$k] = $v;
            }
        }

        return $results;
    }

    public static function convertToEasyPostObject($response, $apiKey)
    {
        $types = array(
            'Address'      => '\EasyPost\Address',
            'ScanForm'     => '\EasyPost\ScanForm',
            'CustomsItem'  => '\EasyPost\CustomsItem',
            'CustomsInfo'  => '\EasyPost\CustomsInfo',
            'Parcel'       => '\EasyPost\Parcel',
            'Shipment'     => '\EasyPost\Shipment',
            'Rate'         => '\EasyPost\Rate',
            'PostageLabel' => '\EasyPost\PostageLabel',
            'Batch'        => '\EasyPost\Batch',
            'Tracker'      => '\EasyPost\Tracker',
            'Event'        => '\EasyPost\Event',
            'Refund'       => '\EasyPost\Refund',
            'Container'    => '\EasyPost\Container',
            'Item'         => '\EasyPost\Item',
            'Order'        => '\EasyPost\Order'
        );

        $prefixes = array(
            'adr'       => '\EasyPost\Address',
            'sf'        => '\EasyPost\ScanForm',
            'cstitem'   => '\EasyPost\CustomsItem',
            'cstinfo'   => '\EasyPost\CustomsInfo',
            'prcl'      => '\EasyPost\Parcel',
            'shp'       => '\EasyPost\Shipment',
            'rate'      => '\EasyPost\Rate',
            'pl'        => '\EasyPost\PostageLabel',
            'batch'     => '\EasyPost\Batch',
            'evt'       => '\EasyPost\Event',
            'rfnd'      => '\EasyPost\Refund',
            'trk'       => '\EasyPost\Tracker',
            'container' => '\EasyPost\Container',
            'item'      => '\EasyPost\Item',
            'order'     => '\EasyPost\Order'
        );

        if (self::isList($response)) {
            $mapped = array();
            foreach ($response as $object => $v) {
                if (is_string($object) && isset($types[$object])) {
                    $v['object'] = $object;
                }
                array_push($mapped, self::convertToEasyPostObject($v, $apiKey));
            }

            return $mapped;
        } else if (is_array($response)) {
            if (isset($response['object']) && is_string($response['object']) && isset($types[$response['object']])) {
                $class = $types[$response['object']];
            } else if (isset($response['id']) && isset($prefixes[substr($response['id'], 0, strpos($response['id'], "_"))])) {
                $class = $prefixes[substr($response['id'], 0, strpos($response['id'], "_"))];
            } else {
                $class = '\EasyPost\Object';
            }

            return Object::constructFrom($response, $class, $apiKey);
        } else {

            return $response;
        }
    }
}

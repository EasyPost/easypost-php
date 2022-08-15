<?php

namespace EasyPost;

/**
 * @package EasyPost
 * @property string $id
 * @property string $object
 * @property string $mode
 * @property string $tracking_code
 * @property string $status
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
class Tracker extends EasypostResource
{
    /**
     * Retrieve a tracker.
     *
     * @param string $id
     * @param string $apiKey
     * @return mixed
     */
    public static function retrieve($id, $apiKey = null)
    {
        return self::retrieveResource(get_class(), $id, $apiKey);
    }

    /**
     * Retrieve all trackers.
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return mixed
     */
    public static function all($params = null, $apiKey = null)
    {
        return self::allResources(get_class(), $params, $apiKey);
    }

    /**
     * Create a tracker.
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        if (!is_array($params)) {
            $clone = $params;
            unset($params);
            $params['tracker']['tracking_code'] = $clone;
        } elseif (!isset($params['tracker']) || !is_array($params['tracker'])) {
            $clone = $params;
            unset($params);
            $params['tracker'] = $clone;
        }

        return self::createResource(get_class(), $params, $apiKey);
    }

    /**
     * Create a list of trackers.
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return bool
     */
    public static function create_list($params = null, $apiKey = null)
    {
        if (!isset($params['trackers']) || !is_array($params['trackers'])) {
            $clone = $params;
            unset($params);
            $params['trackers'] = (object)$clone;
        }

        $encodedParams = str_replace('\\', '', json_encode($params));

        $requestor = new Requestor($apiKey);
        $url = self::classUrl(get_class());
        list($response, $apiKey) = $requestor->request('post', $url . '/create_list', $encodedParams);

        // The response is empty, we return true if no error
        return true;
    }
}

<?php

namespace EasyPost;

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
class Pickup extends EasypostResource
{
    /**
     * Retrieve a pickup.
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
     * Create a pickup.
     *
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        if (!isset($params['pickup']) || !is_array($params['pickup'])) {
            $clone = $params;
            unset($params);
            $params['pickup'] = $clone;
        }

        return self::createResource(get_class(), $params, $apiKey);
    }

    /**
     * Buy a pickup.
     *
     * @param mixed $params
     * @return $this
     * @throws \EasyPost\Error
     */
    public function buy($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/buy';

        list($response, $apiKey) = $requestor->request('post', $url, $params);
        $this->refreshFrom($response, $apiKey, true);

        return $this;
    }

    /**
     * Cancel a pickup.
     *
     * @param mixed $params
     * @return $this
     * @throws \EasyPost\Error
     */
    public function cancel($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/cancel';

        list($response, $apiKey) = $requestor->request('post', $url, $params);
        $this->refreshFrom($response, $apiKey, true);

        return $this;
    }

    /**
     * Get the lowest rate for the pickup.
     *
     * @param array $carriers
     * @param array $services
     * @return Rate
     * @throws \EasyPost\Error
     */
    public function lowest_rate($carriers = [], $services = [])
    {
        $lowestRate = Util::getLowestObjectRate($this, $carriers, $services, 'pickup_rates');

        return $lowestRate;
    }
}

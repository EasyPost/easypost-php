<?php

namespace EasyPost;

/**
 * @package EasyPost
 * @property string $id
 * @property string $reference
 * @property string $object
 * @property string $mode
 * @property string $state
 * @property int $num_shipments
 * @property array $shipments
 * @property object $status
 * @property string $label_url
 * @property ScanForm $scan_form
 * @property Pickup $pickup
 * @property string $created_at
 * @property string $updated_at
 */
class Batch extends EasypostResource
{
    /**
     * Retrieve a batch.
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
     * Retrieve all batches.
     *
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     */
    public static function all($params = null, $apiKey = null)
    {
        return self::allResources(get_class(), $params, $apiKey);
    }

    /**
     * Create a batch.
     *
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        if (!isset($params['batch']) || !is_array($params['batch'])) {
            $clone = $params;
            unset($params);
            $params['batch'] = $clone;
        }

        return self::createResource(get_class(), $params, $apiKey);
    }

    /**
     * Create and buy a batch.
     *
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create_and_buy($params = null, $apiKey = null)
    {
        if (!isset($params['batch']) || !is_array($params['batch'])) {
            $clone = $params;
            unset($params);

            $shipments = (object)[];

            foreach ($clone as $index => $shipment) {
                $shipments->$index = $shipment;
            }

            $params = (object)[
                'batch' => (object)[
                    'shipment' => $shipments,
                ],
            ];
        }

        $encodedParams = str_replace('\\', '', json_encode($params));

        $requestor = new Requestor($apiKey);
        $url = self::classUrl(get_class());
        list($response, $apiKey) = $requestor->request('post', $url . '/create_and_buy', $encodedParams);

        return Util::convertToEasyPostObject($response, $apiKey);
    }

    /**
     * Buy a batch.
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
     * Create a batch label.
     *
     * @param mixed $params
     * @return $this
     * @throws \EasyPost\Error
     */
    public function label($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/label';

        list($response, $apiKey) = $requestor->request('post', $url, $params);
        $this->refreshFrom($response, $apiKey, true);

        return $this;
    }

    /**
     * Remove shipments from a batch.
     *
     * @param mixed $params
     * @return $this
     * @throws \EasyPost\Error
     */
    public function remove_shipments($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/remove_shipments';

        list($response, $apiKey) = $requestor->request('post', $url, $params);
        $this->refreshFrom($response, $apiKey, true);

        return $this;
    }

    /**
     * Add shipments to a batch.
     *
     * @param mixed $params
     * @return $this
     * @throws \EasyPost\Error
     */
    public function add_shipments($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/add_shipments';

        list($response, $apiKey) = $requestor->request('post', $url, $params);
        $this->refreshFrom($response, $apiKey, true);

        return $this;
    }

    /**
     * Create a batch scanform.
     *
     * @param mixed $params
     * @return mixed
     * @throws \EasyPost\Error
     */
    public function create_scan_form($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/scan_form';

        list($response, $apiKey) = $requestor->request('post', $url, $params);

        return Util::convertToEasyPostObject($response, $apiKey);
    }
}

<?php

namespace EasyPost;

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
class Shipment extends EasypostResource
{
    /**
     * Retrieve a shipment.
     *
     * @param string $id
     * @param string $apiKey
     * @return mixed
     */
    public static function retrieve($id, $apiKey = null)
    {
        return self::_retrieve(get_class(), $id, $apiKey);
    }

    /**
     * Retrieve all shipments.
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return mixed
     */
    public static function all($params = null, $apiKey = null)
    {
        return self::_all(get_class(), $params, $apiKey);
    }

    /**
     * Create a shipment.
     *
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        if (!isset($params['shipment']) || !is_array($params['shipment'])) {
            $clone = $params;
            unset($params);
            $params['shipment'] = $clone;
        }

        return self::_create(get_class(), $params, $apiKey);
    }

    /**
     * Re-rate a shipment.
     *
     * @param mixed $params
     * @return $this
     * @throws \EasyPost\Error
     */
    public function regenerate_rates($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/rerate';
        list($response, $apiKey) = $requestor->request('post', $url, $params);
        $this->refreshFrom($response, $apiKey, true);

        return $this;
    }

    /**
     * Get smartrates for a shipment.
     *
     * @return array
     * @throws \EasyPost\Error
     */
    public function get_smartrates()
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/smartrate';
        list($response, $_) = $requestor->request('get', $url);

        return isset($response['result']) ? $response['result'] : [];
    }

    /**
     * Buy a shipment.
     *
     * @param mixed $params
     * @return $this
     * @throws \EasyPost\Error
     */
    public function buy($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/buy';

        if (isset($params['id']) && (!isset($params['rate']) || !is_array($params['rate']))) {
            $clone = $params;
            unset($params);
            $params['rate'] = $clone;
        }

        list($response, $apiKey) = $requestor->request('post', $url, $params);
        $this->refreshFrom($response, $apiKey, true);

        return $this;
    }

    /**
     * Refund a shipment.
     *
     * @param mixed $params
     * @return $this
     * @throws \EasyPost\Error
     */
    public function refund($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/refund';

        list($response, $apiKey) = $requestor->request('get', $url, $params);
        $this->refreshFrom($response, $apiKey, true);

        return $this;
    }

    /**
     * Convert the label format of the shipment.
     *
     * @param mixed $params
     * @return $this
     * @throws \EasyPost\Error
     */
    public function label($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/label';

        if (!isset($params['file_format'])) {
            $clone = $params;
            unset($params);
            $params['file_format'] = $clone;
        }

        list($response, $apiKey) = $requestor->request('get', $url, $params);
        $this->refreshFrom($response, $apiKey);

        return $this;
    }

    /**
     * Insure the shipment.
     *
     * @param mixed $params
     * @return $this
     * @throws \EasyPost\Error
     */
    public function insure($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/insure';

        if (!isset($params['amount'])) {
            $clone = $params;
            unset($params);
            $params['amount'] = $clone;
        }

        list($response, $apiKey) = $requestor->request('post', $url, $params);
        $this->refreshFrom($response, $apiKey);

        return $this;
    }

    /**
     * Generate a form for the shipment.
     *
     * @param string $formType
     * @param mixed $formOptions
     * @return $this
     * @throws \EasyPost\Error
     */
    public function generate_form(string $formType, $formOptions = null): Shipment
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/forms';
        $formOptions['type'] = $formType;

        $params['form'] = $formOptions;

        list($response, $apiKey) = $requestor->request('post', $url, $params);
        $this->refreshFrom($response, $apiKey);

        return $this;
    }

    /**
     * Get the lowest rate for the shipment.
     *
     * @param array $carriers
     * @param array $services
     * @return Rate
     * @throws \EasyPost\Error
     */
    public function lowest_rate($carriers = [], $services = [])
    {
        $lowest_rate = Util::getLowestObjectRate($this, $carriers, $services);

        return $lowest_rate;
    }

    /**
     * Get the lowest smartrate of the shipment.
     *
     * @param int $delivery_days
     * @param string $delivery_accuracy
     * @return Rate
     * @throws \EasyPost\Error
     */
    public function lowest_smartrate($delivery_days, $delivery_accuracy)
    {
        $smartrates = $this->get_smartrates();
        $lowest_rate = $this->get_lowest_smartrate($smartrates, $delivery_days, $delivery_accuracy);

        return $lowest_rate;
    }

    /**
     * Get the lowest smartrate from a list of smartrates.
     *
     * @param array $smartrates
     * @param int $delivery_days
     * @param string $delivery_accuracy
     * @return Rate
     * @throws \EasyPost\Error
     */
    public static function get_lowest_smartrate($smartrates, $delivery_days, $delivery_accuracy)
    {
        $valid_delivery_accuracy_values = [
            'percentile_50',
            'percentile_75',
            'percentile_85',
            'percentile_90',
            'percentile_95',
            'percentile_97',
            'percentile_99',
        ];
        $lowest_smartrate = false;

        if (!in_array(strtolower($delivery_accuracy), $valid_delivery_accuracy_values)) {
            $json_valid_list = json_encode($valid_delivery_accuracy_values);
            throw new Error("Invalid delivery_accuracy value, must be one of: $json_valid_list");
        }

        foreach ($smartrates as $rate) {
            if ($rate['time_in_transit'][$delivery_accuracy] > intval($delivery_days)) {
                continue;
            } elseif (!$lowest_smartrate || floatval($rate['rate']) < floatval($lowest_smartrate['rate'])) {
                $lowest_smartrate = $rate;
            }
        }

        if ($lowest_smartrate == false) {
            throw new Error("No rates found.");
        }

        return $lowest_smartrate;
    }
}

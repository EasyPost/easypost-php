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
     * Get the lowest rate for the shipment.
     *
     * @param array $carriers
     * @param array $services
     * @return bool
     * @throws \EasyPost\Error
     */
    public function lowest_rate($carriers = [], $services = [])
    {
        $lowest_rate = false;
        $carriers_include = [];
        $carriers_exclude = [];
        $services_include = [];
        $services_exclude = [];

        if (!is_array($carriers)) {
            $carriers = explode(',', $carriers);
        }
        for ($a = 0, $b = count($carriers); $a < $b; $a++) {
            $carriers[$a] = trim(strtolower($carriers[$a]));
            if (substr($carriers[$a], 0, 1) == '!') {
                $carriers_exclude[] = substr($carriers[$a], 1);
            } else {
                $carriers_include[] = $carriers[$a];
            }
        }

        if (!is_array($services)) {
            $services = explode(',', $services);
        }
        for ($c = 0, $d = count($services); $c < $d; $c++) {
            $services[$c] = trim(strtolower($services[$c]));
            if (substr($services[$c], 0, 1) == '!') {
                $services_exclude[] = substr($services[$c], 1);
            } else {
                $services_include[] = $services[$c];
            }
        }

        for ($i = 0, $k = count($this->rates); $i < $k; $i++) {
            $rate_carrier = strtolower($this->rates[$i]->carrier);
            if (!empty($carriers_include[0]) && !in_array($rate_carrier, $carriers_include)) {
                continue;
            }
            if (!empty($carriers_exclude[0]) && in_array($rate_carrier, $carriers_exclude)) {
                continue;
            }

            $rate_service = strtolower($this->rates[$i]->service);
            if (!empty($services_include[0]) && !in_array($rate_service, $services_include)) {
                continue;
            }
            if (!empty($services_exclude[0]) && in_array($rate_service, $services_exclude)) {
                continue;
            }

            if (!$lowest_rate || floatval($this->rates[$i]->rate) < floatval($lowest_rate->rate)) {
                $lowest_rate = clone ($this->rates[$i]);
            }
        }

        if ($lowest_rate == false) {
            throw new Error('No rates found.');
        }

        return $lowest_rate;
    }
}

<?php

namespace EasyPost;

class Shipment extends EasypostResource
{
    /**
     * retrieve a shipment
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
     * retrieve all shipments
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
     * save a shipment
     *
     * @return $this
     */
    public function save()
    {
        return self::_save(get_class());
    }

    /**
     * create a shipment
     *
     * @param mixed  $params
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
     * create a shipment from tracking code
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create_from_tracking_code($params = null, $apiKey = null)
    {
        $class = get_class();
        if (!isset($params['tracking_code'])) {
            $clone = $params;
            unset($params);
            $params['tracking_code'] = $clone;
        }

        $requestor = new Requestor($apiKey);
        $url = self::classUrl($class) . '/create_from_tracking_code';
        list($response, $apiKey) = $requestor->request('post', $url, $params);
        return Util::convertToEasyPostObject($response, $apiKey);
    }

    /**
     * get rates for a shipment
     *
     * @param mixed $params
     * @return $this
     * @throws \EasyPost\Error
     */
    public function get_rates($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/rates';
        list($response, $apiKey) = $requestor->request('get', $url, $params);
        $this->refreshFrom($response, $apiKey, true);

        return $this;
    }

    /**
     * buy a shipment
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
     * refund a shipment
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
     * get the shipment barcode
     *
     * @param mixed $params
     * @return mixed
     * @throws \EasyPost\Error
     */
    public function barcode($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/barcode';

        list($response, $apiKey) = $requestor->request('get', $url, $params);

        return $response['barcode_url'];
    }

    /**
     * get the shipment stamp
     *
     * @param mixed $params
     * @return mixed
     * @throws \EasyPost\Error
     */
    public function stamp($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/stamp';

        list($response, $apiKey) = $requestor->request('get', $url, $params);

        return $response['stamp_url'];
    }

    /**
     * get the shipment label
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
     * insure the shipment
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
     * get the lowest rate for the shipment
     *
     * @param array $carriers
     * @param array $services
     * @return bool
     * @throws \EasyPost\Error
     */
    public function lowest_rate($carriers=array(), $services=array())
    {
        $lowest_rate = false;
        $carriers_include = array();
        $carriers_exclude = array();
        $services_include = array();
        $services_exclude = array();

        if(!is_array($carriers)) {
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

        if(!is_array($services)) {
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
                $lowest_rate = clone($this->rates[$i]);
            }
        }

        if ($lowest_rate == false) {
            throw new Error('No rates found.');
        }

        return $lowest_rate;
    }

}


<?php

namespace EasyPost;

class Shipment extends Resource
{
    public static function constructFrom($values, $class = null, $apiKey = null)
    {
        $class = get_class();

        return self::constructFrom($values, $class, $apiKey);
    }

    public static function retrieve($id, $apiKey = null)
    {
        $class = get_class();

        return self::_retrieve($class, $id, $apiKey);
    }

    public static function all($params = null, $apiKey = null)
    {
        $class = get_class();

        return self::_all($class, $params, $apiKey);
    }

    public static function create($params = null, $apiKey = null)
    {
        $class = get_class();
        if (!isset($params['shipment']) || !is_array($params['shipment'])) {
            $clone = $params;
            unset($params);
            $params['shipment'] = $clone;
        }

        return self::_create($class, $params, $apiKey);
    }

    public function save()
    {
        $class = get_class();

        return self::_save($class);
    }

    public function get_rates($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/rates';
        list($response, $apiKey) = $requestor->request('get', $url, $params);
        $this->refreshFrom($response, $apiKey, true);

        return $this;
    }

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

    public function refund($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/refund';

        list($response, $apiKey) = $requestor->request('get', $url, $params);
        $this->refreshFrom($response, $apiKey, true);

        return $this;
    }

    public function barcode($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/barcode';

        list($response, $apiKey) = $requestor->request('get', $url, $params);

        return $response['barcode_url'];
    }

    public function stamp($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/stamp';

        list($response, $apiKey) = $requestor->request('get', $url, $params);

        return $response['stamp_url'];
    }

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

    public function lowest_rate($carriers=array(), $services=array())
    {
        $lowest_rate = false;

        if(!is_array($carriers)) {
            $carriers = explode(',', $carriers);
        }
        $carriers = array_map('strtolower', $carriers);

        if(!is_array($services)) {
            $services = explode(',', $services);
        }
        $services = array_map('strtolower', $services);

        for ($i = 0, $k = count($this->rates); $i < $k; $i++) {
            $rate_carrier = strtolower($this->rates[$i]->carrier);
            if (!empty($carriers[0]) && !in_array($rate_carrier, $carriers)) {
                continue;
            }

            $rate_service = strtolower($this->rates[$i]->service);
            if (!empty($services[0]) && !in_array($rate_service, $services)) {
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

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

    public function get_rates($params = null, $apiKey = null)
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

    public function track($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/track';

        list($response, $apiKey) = $requestor->request('get', $url, $params);
        // $this->refreshFrom($response, $apiKey, true);

        // return $this;
        return $response;
    }

    public function lowest_rate($carriers=null)
    {
        $lowest_rate = false;

        for ($i = 0, $k = count($this->rates); $i < $k; $i++) {
            if (!$lowest_rate || floatval($this->rates[$i]->rate) < floatval($lowest_rate->rate)) {
                if (empty($carriers)) {          
                    $lowest_rate = clone($this->rates[$i]);
                } else {
                    $rate_carrier = strtolower($this->rates[$i]->carrier);

                    if (is_array($carriers)) {
                        $carriers = array_map('strtolower', $carriers);
                        if(in_array($rate_carrier, $carriers)) {
                            $lowest_rate = clone($this->rates[$i]);
                        }
                    } else {
                        if (strtolower($carriers) == $rate_carrier) {
                            $lowest_rate = clone($this->rates[$i]);
                        }
                    }
                }
            }
        }

        return $lowest_rate;
    }

    // public function find_rate($carriers=null, $services=null, $max_days) {
    //   $rates = clone($this->rates);

    //   if($carriers !== null) {
    //     if(!is_array($carriers)) {
    //       $carriers = explode(',', $carriers);
    //     }
    //     $carriers = array_map('strtolower', $carriers);
    //     for($i = 0, $j = count($rates); $i < $j; $i++) {
    //       if(!in_array(strtolower($rates[$i]->carrier), $carriers)) {
    //         unset($rates[$i]);
    //       }
    //     }
    //     $rates = array_values($rates);
    //   }
    // }
}

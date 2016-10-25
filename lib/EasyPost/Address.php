<?php

namespace EasyPost;

class Address extends EasypostResource
{
    /**
     * retrieve an address
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
     * retrieve all addresses
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
     * save an address
     *
     * @return $this
     */
    public function save()
    {
        return self::_save(get_class());
    }

    /**
     * create an address
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        $urlMod = "";

        if ((isset($params['verify']) && is_array($params['verify'])) || (isset($params['verify_strict']) && is_array($params['verify_strict']))) {
            $verify = "";
            if (isset($params['verify'])) {
                $verify = $params['verify'];
                unset($params['verify']);
            }

            $verify_strict = "";
            if (isset($params['verify_strict'])) {
                $verify_strict = $params['verify_strict'];
                unset($params['verify_strict']);
            }

            $urlMod = "?";

            if (is_array($verify)) {
                foreach ($verify as $verification) {
                    $urlMod .= "verify[]=" . $verification . "&";
                }
            }

            if (is_array($verify_strict)) {
                foreach ($verify_strict as $verification_strict) {
                    $urlMod .= "verify_strict[]=" . $verification_strict . "&";
                }
            }
        }

        if (!isset($params['address']) || !is_array($params['address'])) {
            $clone = $params;
            unset($params);
            $params['address'] = $clone;
        }

        return self::_create(get_class(), $params, $apiKey, $urlMod);
    }

    /**
     * create and verify an address
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create_and_verify($params = null, $apiKey = null)
    {
        $class = get_class();
        if (!isset($params['address']) || !is_array($params['address'])) {
            $clone = $params;
            unset($params);
            $params['address'] = $clone;
        }

        $requestor = new Requestor($apiKey);
        $url = self::classUrl($class);
        list($response, $apiKey) = $requestor->request('post', $url.'/create_and_verify', $params);

        if (isset($response['address'])) {
            $verified_address = Util::convertToEasyPostObject($response['address'], $apiKey);
            if (!empty($response['message'])) {
                $verified_address->message = $response['message'];
                $verified_address->_immutableValues[] = 'message';
            }

            return $verified_address;
        } else {

            return Util::convertToEasyPostObject($response, $apiKey);
        }
    }

    /**
     * verify an address
     *
     * @param mixed $params
     * @return mixed
     * @throws \EasyPost\Error
     */
    public function verify($params = null)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl() . '/verify';
        list($response, $apiKey) = $requestor->request('get', $url, $params);
        if (isset($response['address'])) {
            $verified_address = Util::convertToEasyPostObject($response['address'], $apiKey);
            if (!empty($response['message'])) {
                $verified_address->message = $response['message'];
                $verified_address->_immutableValues[] = 'message';
            }

            return $verified_address;
        } else {

            return Util::convertToEasyPostObject($response, $apiKey);
        }
    }
}

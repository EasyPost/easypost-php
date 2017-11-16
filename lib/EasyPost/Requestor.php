<?php

namespace EasyPost;

class Requestor
{
    /**
     * @var string
     */
    public $apiKey;

    /**
     * constructor
     *
     * @param string $apiKey
     */
    public function __construct($apiKey = null)
    {
        $this->_apiKey = $apiKey;
    }

    /**
     * get the API url
     *
     * @param string $url
     * @return string
     */
    public static function apiUrl($url = '')
    {
        $apiBase = EasyPost::$apiBase;

        return "{$apiBase}{$url}";
    }

    /**
     * @param mixed $value
     * @return string
     */
    public static function utf8($value)
    {
        if (is_string($value) && mb_detect_encoding($value, "UTF-8", TRUE) != "UTF-8") {
            return utf8_encode($value);
        }

        return $value;
    }

    /**
     * @param mixed $d
     * @return array|string
     */
    private static function _encodeObjects($d)
    {
        if ($d instanceof EasypostResource) {

            return array("id" => self::utf8($d->id));
        } else if ($d === true) {

            return 'true';
        } else if ($d === false) {

            return 'false';
        } else if (is_array($d)) {
            $res = array();
            foreach ($d as $k => $v) {
                $res[$k] = self::_encodeObjects($v);
            }

            return $res;
        } else {

            return self::utf8($d);
        }
    }

    /**
     * @param mixed $arr
     * @param null  $prefix
     * @return string
     */
    public static function encode($arr, $prefix = null)
    {
        if (!is_array($arr)) {

            return $arr;
        }

        $r = array();
        foreach ($arr as $k => $v) {
            if (is_null($v)) {
                continue;
            }

            if ($prefix && isset($k)) {
                $k = $prefix . "[" . $k . "]";
            } else if ($prefix) {
                $k = $prefix . "[]";
            }

            if (is_array($v)) {
                $r[] = self::encode($v, $k, true);
            } else {
                $r[] = urlencode($k) . "=" . urlencode($v);
            }
        }

        return implode("&", $r);
    }

    /**
     * @param string $method
     * @param string $url
     * @param mixed  $params
     * @return array
     * @throws \EasyPost\Error
     */
    public function request($method, $url, $params = null, $apiKeyRequired = true)
    {
        if (!$params) {
            $params = array();
        }
        list($httpBody, $httpStatus, $myApiKey) = $this->_requestRaw($method, $url, $params, $apiKeyRequired);
        $response = $this->_interpretResponse($httpBody, $httpStatus);

        return array($response, $myApiKey);
    }

    /**
     * @param string $method
     * @param string $url
     * @param mixed  $params
     * @return array
     * @throws \EasyPost\Error
     */
    private function _requestRaw($method, $url, $params, $apiKeyRequired)
    {
        $myApiKey = $this->_apiKey;

        if (!$myApiKey) {
            if (!$myApiKey = EasyPost::$apiKey) {
                if ($apiKeyRequired) {
                    throw new Error('No API key provided. Set your API key using "EasyPost::setApiKey(<API-KEY>)". See https://www.easypost.com/docs for details, or contact support@easypost.com for assistance.');
                }
            }
        }

        $absUrl = $this->apiUrl($url);
        $params = self::_encodeObjects($params);

        $langVersion = phpversion();
        $uname = php_uname();
        $ua = array(
            'bindings_version' => EasyPost::VERSION,
            'lang'             => 'php',
            'lang_version'     => $langVersion,
            'publisher'        => 'easypost',
            'uname'            => $uname
        );
        $headers = array('X-Client-User-Agent: ' . json_encode($ua),
            'User-Agent: EasyPost/v2 PhpClient/' . EasyPost::VERSION,
            "Authorization: Bearer {$myApiKey}");
        if (EasyPost::$apiVersion) {
            $headers[] = 'EasyPost-Version: ' . EasyPost::$apiVersion;
        }
        list($httpBody, $httpStatus) = $this->_curlRequest($method, $absUrl, $headers, $params, $myApiKey);

        return array($httpBody, $httpStatus, $myApiKey);
    }

    /**
     * @param string $method
     * @param string $absUrl
     * @param mixed  $headers
     * @param mixed  $params
     * @param string $myApiKey
     * @return array
     * @throws \EasyPost\Error
     */
    private function _curlRequest($method, $absUrl, $headers, $params, $myApiKey)
    {
        $curl = curl_init();
        $method = strtolower($method);
        $curlOptions = array();

        // method
        if ($method == 'get') {
            $curlOptions[CURLOPT_HTTPGET] = 1;
            if (count($params) > 0) {
                $encoded = self::encode($params);
                $absUrl = "$absUrl?$encoded";
            }
        } else if ($method == 'post') {
            $curlOptions[CURLOPT_POST] = 1;
            $curlOptions[CURLOPT_POSTFIELDS] = self::encode($params);
        } else if ($method == 'delete') {
            $curlOptions[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
            if (count($params) > 0) {
                $encoded = self::encode($params);
                $absUrl = "{$absUrl}?{$encoded}";
            }
        } else if ($method == 'patch' || $method == 'put') {
            $curlOptions[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
            if (count($params) > 0) {
                $curlOptions[CURLOPT_POSTFIELDS] = self::encode($params);
            }
        } else {
            throw new Error("Unrecognized method {$method}");
        }

        $absUrl = self::utf8($absUrl);
        $curlOptions[CURLOPT_URL] = $absUrl;
        $curlOptions[CURLOPT_RETURNTRANSFER] = true;
        $curlOptions[CURLOPT_HTTPHEADER] = $headers;

        if ($timeout = EasyPost::getConnectTimeout()) {
            $curlOptions[CURLOPT_CONNECTTIMEOUT_MS] = $timeout;
        }

        if ($timeout = EasyPost::getResponseTimeout()) {
            $curlOptions[CURLOPT_TIMEOUT_MS] = $timeout;
        }

        curl_setopt_array($curl, $curlOptions);
        $httpBody = curl_exec($curl);

        $errorNum = curl_errno($curl);
        if ($errorNum == CURLE_SSL_CACERT || $errorNum == CURLE_SSL_PEER_CERTIFICATE || $errorNum == 77) {
          curl_setopt($curl, CURLOPT_CAINFO, dirname(__FILE__) . '/../cacert.pem');
          $httpBody = curl_exec($curl);
        }

        if ($httpBody === false) {
            $errorNum = curl_errno($curl);
            $message = curl_error($curl);
            curl_close($curl);
            $this->handleCurlError($errorNum, $message);
        }

        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return array($httpBody, $httpStatus);
    }

    /**
     * @param string $httpBody
     * @param int    $httpStatus
     * @return mixed
     * @throws \EasyPost\Error
     */
    private function _interpretResponse($httpBody, $httpStatus)
    {
        try {
            $response = json_decode($httpBody, true);
        } catch (\Exception $e) {
            throw new Error("Invalid response body from API: HTTP Status: ({$httpStatus}) {$httpBody}", $httpStatus, $httpBody);
        }

        if ($httpStatus < 200 || $httpStatus >= 300) {
            $this->handleApiError($httpBody, $httpStatus, $response);
        }
        return $response;
    }

    /**
     * @param string $httpBody
     * @param int    $httpStatus
     * @param array  $response
     * @throws \EasyPost\Error
     */
    public function handleApiError($httpBody, $httpStatus, $response)
    {
        if (!is_array($response) || !isset($response['error'])) {
            throw new Error("Invalid response object from API: HTTP Status: ({$httpStatus}) {$httpBody})", $httpStatus, $httpBody);
        }
        throw new Error(is_array($response['error']) ? $response['error']['message'] : (!empty($response['error']) ? $response['error'] : ""), $httpStatus, $httpBody);
    }

    /**
     * @param int    $errorNum
     * @param string $message
     * @throws \EasyPost\Error
     */
    public function handleCurlError($errorNum, $message)
    {
        $apiBase = EasyPost::$apiBase;
        switch ($errorNum) {
            case CURLE_COULDNT_CONNECT:
            case CURLE_COULDNT_RESOLVE_HOST:
            case CURLE_OPERATION_TIMEOUTED:
                $msg = "Could not connect to EasyPost ({$apiBase}). Please check your internet connection and try again.  If this problem persists please let us know at contact@easypost.com.";
                break;
            case CURLE_SSL_CACERT:
            case CURLE_SSL_PEER_CERTIFICATE:
                $msg = "Could not verify EasyPost's SSL certificate. Please make sure that your network is not intercepting certificates.  (Try going to {$apiBase} in your browser.)  If this problem persists, let us know at contact@easypost.com.";
                break;
            default:
                $msg = "Unexpected error communicating with EasyPost. If this problem persists please let us know at contact@easypost.com.";
        }

        $msg .= "\nNetwork error [errno {$errorNum}]: {$message})";
        throw new Error($msg);
    }
}

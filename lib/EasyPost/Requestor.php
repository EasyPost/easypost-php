<?php

namespace EasyPost;

class Requestor
{
    /**
     * @var string
     */
    private $supportEmail = 'support@easypost.com';

    /**
     * @var string
     */
    public $apiKey;

    /**
     * Constructor.
     *
     * @param string $apiKey
     */
    public function __construct($apiKey = null)
    {
        $this->_apiKey = $apiKey;
    }

    /**
     * Get the API URL.
     *
     * @param string $url
     * @return string
     */
    public static function apiUrl($url = '', $beta = false)
    {
        if ($beta) {
            $apiBase = EasyPost::$betaApiBase;
        } else {
            $apiBase = EasyPost::$apiBase;
        }

        return "{$apiBase}{$url}";
    }

    /**
     * Converts a set of values to UTF-8 encoding.
     *
     * @param mixed $value
     * @return string
     */
    public static function utf8($value)
    {
        if (is_string($value) && mb_detect_encoding($value, 'UTF-8', true) != 'UTF-8') {
            return utf8_encode($value);
        }

        return $value;
    }

    /**
     * Encodes an EasyPost object and prepares the data for the request.
     *
     * @param mixed $data
     * @return array|string
     */
    private static function encodeObjects($data)
    {
        if (is_null($data)) {
            return [];
        } elseif ($data instanceof EasypostResource) {
            return ['id' => self::utf8($data->id)];
        } elseif ($data === true) {
            return 'true';
        } elseif ($data === false) {
            return 'false';
        } elseif (is_array($data)) {
            $resource = [];
            foreach ($data as $k => $v) {
                if (!is_null($v) and ($v !== '') and (!is_array($v) or !empty($v))) {
                    $resource[$k] = self::encodeObjects($v);
                }
            }

            return $resource;
        } else {
            return self::utf8(strval($data));
        }
    }

    /**
     * URL Encodes data for GET requests.
     *
     * @param mixed $arr
     * @param null $prefix
     * @return string
     */
    public static function urlEncode($arr, $prefix = null)
    {
        if (!is_array($arr)) {
            return $arr;
        }

        $r = [];
        foreach ($arr as $k => $v) {
            if (is_null($v)) {
                continue;
            }

            if ($prefix && isset($k)) {
                $k = $prefix . '[' . $k . ']';
            } elseif ($prefix) {
                $k = $prefix . '[]';
            }

            if (is_array($v)) {
                $r[] = self::urlEncode($v, $k, true);
            } else {
                $r[] = urlencode($k) . '=' . urlencode($v);
            }
        }

        return implode('&', $r);
    }

    /**
     * Make a request to the EasyPost API.
     *
     * @param string $method
     * @param string $url
     * @param mixed $params
     * @return array
     * @throws \EasyPost\Error
     */
    public function request($method, $url, $params = null, $apiKeyRequired = true, $beta = false)
    {
        list($httpBody, $httpStatus, $myApiKey) = $this->requestRaw($method, $url, $params, $apiKeyRequired, $beta);
        $response = $this->interpretResponse($httpBody, $httpStatus);

        return [$response, $myApiKey];
    }

    /**
     * Internal logic required to make a request to the EasyPost API.
     *
     * @param string $method
     * @param string $url
     * @param mixed $params
     * @return array
     * @throws \EasyPost\Error
     */
    private function requestRaw($method, $url, $params, $apiKeyRequired = true, $beta = false)
    {
        $myApiKey = $this->_apiKey;

        if (!$myApiKey) {
            if (!$myApiKey = EasyPost::$apiKey) {
                if ($apiKeyRequired) {
                    throw new Error("No API key provided. Set your API key using \"EasyPost::setApiKey(<API-KEY>)\". See https://www.easypost.com/docs for details, or contact {$this->supportEmail} for assistance.");
                }
            }
        }

        $absUrl = $this->apiUrl($url, $beta);
        $params = self::encodeObjects($params);

        $phpVersion = phpversion();
        $osType = php_uname('s');
        $osVersion = php_uname('r');
        $osArch = php_uname('m');

        $headers = [
            'Accept: application/json',
            "Authorization: Bearer {$myApiKey}",
            'Content-Type: application/json',
            'User-Agent: EasyPost/v2 PhpClient/' . EasyPost::VERSION . " PHP/$phpVersion OS/$osType OSVersion/$osVersion OSArch/$osArch",
        ];

        list($httpBody, $httpStatus) = $this->curlRequest($method, $absUrl, $headers, $params);

        return [$httpBody, $httpStatus, $myApiKey];
    }

    /**
     * Build the cURL request.
     *
     * @param string $method
     * @param string $absUrl
     * @param mixed $headers
     * @param mixed $params
     * @return array
     * @throws \EasyPost\Error
     */
    private function curlRequest($method, $absUrl, $headers, $params)
    {
        $curl = curl_init();
        $method = strtolower($method);
        $curlOptions = [];

        // Setup the HTTP method and params to use on the request
        if ($method == 'get') {
            $curlOptions[CURLOPT_HTTPGET] = 1;
            if (isset($params) && !empty($params)) {
                $urlParams = self::urlEncode($params);
                $absUrl = "$absUrl?$urlParams";
            }
        } elseif ($method == 'post') {
            $curlOptions[CURLOPT_POST] = 1;
            if (strpos($absUrl, 'trackers/create_list') !== false || strpos($absUrl, 'batches/create_and_buy') !== false) {
                // We must encode the params for the `trackers/create_list` endpoint differently because
                // it expects a hash of hashes instead of a list of objects (handled in the `create_list` function)
                $curlOptions[CURLOPT_POSTFIELDS] = $params;
            } else {
                $curlOptions[CURLOPT_POSTFIELDS] = json_encode($params);
            }
        } elseif ($method == 'patch' || $method == 'put') {
            $curlOptions[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
            $curlOptions[CURLOPT_POSTFIELDS] = json_encode($params);
        } elseif ($method == 'delete') {
            $curlOptions[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
            if (isset($params) && !empty($params)) {
                $urlParams = self::urlEncode($params);
                $absUrl = "$absUrl?$urlParams";
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

        return [$httpBody, $httpStatus];
    }

    /**
     * Interpret the response body we receive from the API.
     *
     * @param string $httpBody
     * @param int $httpStatus
     * @return mixed
     * @throws \EasyPost\Error
     */
    private function interpretResponse($httpBody, $httpStatus)
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
     * Handles API errors returned from EasyPost.
     *
     * @param string $httpBody
     * @param int $httpStatus
     * @param array $response
     * @throws \EasyPost\Error
     */
    public function handleApiError($httpBody, $httpStatus, $response)
    {
        if (!is_array($response) || !isset($response['error'])) {
            throw new Error("Invalid response object from API: HTTP Status: ({$httpStatus}) {$httpBody})", $httpStatus, $httpBody);
        }

        // Errors from some carriers may be an array improperly assigned to the `message` field, check for those here (eg: GSO)
        // TODO: This is a bandaid fix until the GSO service maps its error messages correctly. More dtails can be found here:
        // https://github.com/EasyPost/easypost-php/pull/189
        if (is_array($response['error'])) {
            $message = is_array($response['error']['message'])
                ? json_encode($response['error']['message'])
                : $response['error']['message'];
        } elseif (!empty($response['error'])) {
            $message = $response['error'];
        }

        throw new Error($message, $httpStatus, $httpBody);
    }

    /**
     * Handle errors related to curling the API.
     *
     * @param int $errorNum
     * @param string $message
     * @throws \EasyPost\Error
     */
    public function handleCurlError($errorNum, $message)
    {
        $apiBase = EasyPost::$apiBase;

        switch ($errorNum) {
            case CURLE_COULDNT_CONNECT:
            case CURLE_COULDNT_RESOLVE_HOST:
            case CURLE_OPERATION_TIMEDOUT:
                $msg = "Could not connect to EasyPost ({$apiBase}). Please check your internet connection and try again.  If this problem persists please let us know at {$this->supportEmail}.";
                break;
            case CURLE_SSL_CACERT:
            case CURLE_SSL_PEER_CERTIFICATE:
                $msg = "Could not verify EasyPost's SSL certificate. Please make sure that your network is not intercepting certificates.  (Try going to {$apiBase} in your browser.)  If this problem persists, let us know at {$this->supportEmail}.";
                break;
            default:
                $msg = "Unexpected error communicating with EasyPost. If this problem persists please let us know at {$this->supportEmail}.";
        }

        $msg .= "\nNetwork error [errno {$errorNum}]: {$message})";
        throw new Error($msg);
    }
}

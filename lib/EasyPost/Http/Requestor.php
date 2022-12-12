<?php

namespace EasyPost\Http;

use EasyPost\Constant\Constants;
use EasyPost\EasypostObject;
use EasyPost\Exception\Error;
use GuzzleHttp\Client;

class Requestor
{
    /**
     * Constructor.
     *
     * @param EasyPostClient $client
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * Get the API URL.
     *
     * @param string $url
     * @param bool $beta
     * @return string
     */
    private function absoluteUrl($url = '', $beta = false)
    {
        if ($beta) {
            $apiBase = Constants::API_BASE . '/' . Constants::BETA_API_VERSION;
        } else {
            $apiBase = $this->client->apiBase . '/' . Constants::API_VERSION;
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
        } elseif ($data instanceof EasypostObject) {
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
                $r[] = self::urlEncode($v, $k);
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
     * @param bool $beta
     * @return array
     * @throws \EasyPost\Exception\Error
     */
    public function request($method, $url, $params = null, $beta = false)
    {
        list($responseBody, $httpStatus) = $this->requestRaw($method, $url, $params, $beta);
        $httpBody = $this->interpretResponse($responseBody, $httpStatus);

        return $httpBody;
    }

    /**
     * Internal logic required to make a request to the EasyPost API.
     *
     * @param string $method
     * @param string $url
     * @param mixed $params
     * @param bool $beta
     * @return array
     * @throws \EasyPost\Exception\Error
     */
    private function requestRaw($method, $url, $params, $beta = false)
    {
        $absoluteUrl = $this->absoluteUrl($url, $beta);
        $requestOptions = [
            'http_errors' => false, // we set this false here so we can do our own error handling
            'timeout' => $this->client->timeout,
        ];
        $params = self::encodeObjects($params);
        if (in_array(strtolower($method), ['get', 'delete'])) {
            $requestOptions['query'] = $params;
        } else {
            $requestOptions['json'] = $params;
        }

        $phpVersion = phpversion();
        $osType = php_uname('s');
        $osVersion = php_uname('r');
        $osArch = php_uname('m');

        $headers = [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$this->client->apiKey}",
            'Content-Type' => 'application/json',
            'User-Agent' => 'EasyPost/v2 PhpClient/' . Constants::LIBRARY_VERSION . " PHP/$phpVersion OS/$osType OSVersion/$osVersion OSArch/$osArch",
        ];

        $guzzleClient = new Client();
        $requestOptions['headers'] = $headers;
        try {
            $response = $guzzleClient->request($method, $absoluteUrl, $requestOptions);
        } catch (\GuzzleHttp\Exception\ConnectException $error) {
            $message = "Unexpected error communicating with EasyPost. If this problem persists please let us know at {$this->supportEmail}. {$error->getMessage()}";
            throw new Error($message, null, null);
        }

        // Guzzle does not have a native way of catching timeout exceptions... If we don't have a response at this point, it's likely due to a timeout
        if (!isset($response)) {
            throw new Error('Did not receive a response from the API.', null, null);
        }

        $responseBody = $response->getBody();
        $httpStatus = $response->getStatusCode();

        return [$responseBody, $httpStatus];
    }

    /**
     * Interpret the response body we receive from the API.
     *
     * @param string $httpBody
     * @param int $httpStatus
     * @return mixed
     * @throws \EasyPost\Exception\Error
     */
    public function interpretResponse($httpBody, $httpStatus)
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
     * @throws \EasyPost\Exception\Error
     */
    public function handleApiError($httpBody, $httpStatus, $response)
    {
        if (!is_array($response) || !isset($response['error'])) {
            throw new Error("Invalid response object from API: HTTP Status: ({$httpStatus}) {$httpBody})", $httpStatus, $httpBody);
        }

        // Errors may be an array improperly assigned to the `message` field instead of the `errors` field, concatenate those here
        if (is_array($response['error'])) {
            $message = is_array($response['error']['message'])
                ? json_encode($response['error']['message'])
                : $response['error']['message'];
        } elseif (!empty($response['error'])) {
            $message = $response['error'];
        }

        throw new Error($message, $httpStatus, $httpBody);
    }
}

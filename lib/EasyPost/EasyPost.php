<?php

namespace EasyPost;

abstract class EasyPost
{
    /**
     * The API key of the authenticated user.
     *
     * @var string
     */
    public static $apiKey;

    /**
     * The base url to use for all requests.
     *
     * @var string
     */
    public static $apiBase = 'https://api.easypost.com/v2';

    /**
     * The base beta url to use for all requests.
     *
     * @var string
     */
    public static $betaApiBase = 'https://api.easypost.com/beta';

    /**
     * The API version used in requests.
     *
     * @var string
     */
    public static $apiVersion = '2';

    /**
     * Time in milliseconds to wait for a connection.
     *
     * Zero or null means no timeout.
     *
     * @var int|null
     */
    public static $connectTimeout = 30000;

    /**
     * Time in milliseconds to wait for a response.
     *
     * Zero or null means no timeout.
     *
     * @var int|null
     */
    public static $responseTimeout = 60000;

    /**
     * The version of this PHP client library.
     *
     * @var string
     */
    const VERSION = '5.6.0';

    /**
     * Get the API key.
     *
     * @return string
     */
    public static function getApiKey()
    {
        return self::$apiKey;
    }

    /**
     * Set the API key.
     *
     * @param string $apiKey
     */
    public static function setApiKey($apiKey)
    {
        self::$apiKey = $apiKey;
    }

    /**
     * Get the API base URL.
     *
     * @return string
     */
    public static function getApiBase()
    {
        return self::$apiBase;
    }

    /**
     * Set the API base URL.
     *
     * @param string $apiBase
     */
    public static function setApiBase($apiBase)
    {
        self::$apiBase = $apiBase;
    }

    /**
     * Get the API version.
     *
     * @return string
     */
    public static function getApiVersion()
    {
        return self::$apiVersion;
    }

    /**
     * Set the API version.
     *
     * @param $apiVersion
     */
    public static function setApiVersion($apiVersion)
    {
        self::$apiVersion = $apiVersion;
    }

    /**
     * Get time in milliseconds to wait for a connection.
     *
     * Zero or null means no timeout.
     *
     * @return int|null
     */
    public static function getConnectTimeout()
    {
        return self::$connectTimeout;
    }

    /**
     * Set time in milliseconds to wait for a connection.
     *
     * Zero or null means no timeout.
     *
     * @param int|null $connectTimeout
     */
    public static function setConnectTimeout($connectTimeout)
    {
        self::$connectTimeout = $connectTimeout;
    }

    /**
     * Get time in milliseconds to wait for a response.
     *
     * Zero or null means no timeout.
     *
     * @return int|null
     */
    public static function getResponseTimeout()
    {
        return self::$responseTimeout;
    }

    /**
     * Set time in milliseconds to wait for a response.
     *
     * Zero or null means no timeout.
     *
     * @param int|null $responseTimeout
     */
    public static function setResponseTimeout($responseTimeout)
    {
        self::$responseTimeout = $responseTimeout;
    }
}

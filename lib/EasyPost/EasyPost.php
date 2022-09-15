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
     * Time in seconds to wait for a response.
     *
     * Zero or null means no timeout.
     *
     * @var float|null
     */
    public static $timeout = 60.0;

    /**
     * The version of this PHP client library.
     *
     * @var string
     */
    const VERSION = '5.8.0';

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
     * Get timeout in seconds.
     *
     * Zero or null means no timeout.
     *
     * @return int|null
     */
    public static function getTimeout()
    {
        return self::$timeout;
    }

    /**
     * Set timeout in seconds.
     *
     * Zero or null means no timeout.
     *
     * @param float|null $timeout
     */
    public static function setTimeout($timeout)
    {
        self::$timeout = $timeout;
    }
}

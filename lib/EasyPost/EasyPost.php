<?php

namespace EasyPost;

abstract class EasyPost
{
    /**
     * @var string
     */
    public static $apiKey;

    /**
     * @var string
     */
    public static $apiBase = 'https://api.easypost.com/v2';

    /**
     * @var string
     */
    public static $apiVersion = "2";

    /**
     * @var string
     */
    const VERSION = '3.1.0';

    /**
     * get the API key
     *
     * @return string
     */
    public static function getApiKey()
    {
        return self::$apiKey;
    }

    /**
     * set the API key
     *
     * @param string $apiKey
     */
    public static function setApiKey($apiKey)
    {
        self::$apiKey = $apiKey;
    }

    /**
     * get the API base URL
     *
     * @return string
     */
    public static function getApiBase()
    {
        return self::$apiBase;
    }

    /**
     * set the API base URL
     *
     * @param string $apiBase
     */
    public static function setApiBase($apiBase)
    {
        self::$apiBase = $apiBase;
    }

    /**
     * get the API version
     *
     * @return string
     */
    public static function getApiVersion()
    {
        return self::$apiVersion;
    }

    /**
     * set the API version
     *
     * @param $apiVersion
     */
    public static function setApiVersion($apiVersion)
    {
        self::$apiVersion = $apiVersion;
    }
}

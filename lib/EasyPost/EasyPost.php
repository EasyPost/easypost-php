<?php

namespace EasyPost;

abstract class EasyPost
{
    public static $apiKey;
    public static $apiBase = 'https://api.easypost.com/v2';
    public static $apiVersion = "2";
    public static $timeout = 120;
    public static $connectTimeout = 30;
    const VERSION = '2.1.1';

    public static function getApiKey()
    {
        return self::$apiKey;
    }
    public static function setApiKey($apiKey)
    {
        self::$apiKey = $apiKey;
    }

    public static function getApiBase()
    {
        return self::$apiBase;
    }
    public static function setApiBase($apiBase)
    {
        self::$apiBase = $apiBase;
    }

    public static function getApiVersion()
    {
        return self::$apiVersion;
    }
    public static function setApiVersion($apiVersion)
    {
        self::$apiVersion = $apiVersion;
    }

    public static function getTimeout()
    {
        return self::$timeout;
    }
    public static function setTimeout($timeout)
    {
        self::$timeout = $timeout;
    }

    public static function getConnectTimeout()
    {
        return self::$connectTimeout;
    }
    public static function setConnectTimeout($connectTimeout)
    {
        self::$connectTimeout = $connectTimeout;
    }
}

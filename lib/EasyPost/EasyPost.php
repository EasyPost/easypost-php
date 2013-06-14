<?php

namespace EasyPost;

abstract class EasyPost
{
    public static $apiKey;
    public static $apiBase = 'https://api.easypost.com/v2';
    public static $apiVersion = "2";
    const VERSION = '1.2';

    public static function getApiKey()
    {
        return self::$apiKey;
    }

    public static function setApiKey($apiKey)
    {
        self::$apiKey = $apiKey;
    }

    public static function getApiVersion()
    {
        return self::$apiVersion;
    }

    public static function setApiVersion($apiVersion)
    {
        self::$apiVersion = $apiVersion;
    }
}

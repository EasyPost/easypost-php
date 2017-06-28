<?php

namespace EasyPost;

class Rating extends EasypostResource
{
	/**
     * create an address
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {    	
    	return self::_create(get_class(), $params, $apiKey);
    }
}
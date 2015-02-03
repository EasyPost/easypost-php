<?php

namespace EasyPost;

class Pickup extends Resource
{
	    public static function retrieve($id, $apiKey = null)
    {
        $class = get_class();

        return self::_retrieve($class, $id, $apiKey);
    }
}
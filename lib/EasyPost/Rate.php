<?php

namespace EasyPost;

class Rate extends EasypostResource
{
    /**
     * retrieve a rate
     *
     * @param string $id
     * @param string $apiKey
     * @return mixed
     */
    public static function retrieve($id, $apiKey = null)
    {
        return self::_retrieve(get_class(), $id, $apiKey);
    }
}

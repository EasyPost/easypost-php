<?php

namespace EasyPost;

class ScanForm extends Resource
{
    public static function retrieve($id, $apiKey = null)
    {
        return self::_retrieve(get_class(), $id, $apiKey);
    }

    public static function all($params = null, $apiKey = null)
    {
        return self::_all(get_class(), $params, $apiKey);
    }

    public function save()
    {
        return self::_save(get_class());
    }

    public static function create($params = null, $apiKey = null)
    {
        if (!isset($params['scan_form']) || !is_array($params['scan_form'])) {
            $clone = $params;
            unset($params);
            $params['scan_form'] = $clone;
        }

        return self::_create(get_class(), $params, $apiKey);
    }
}


<?php

namespace EasyPost;

/**
 * @package EasyPost
 * @property string $id
 * @property string $object
 * @property string $eel_pfc
 * @property string $contents_type
 * @property string $contents_explanation
 * @property bool $customs_certify
 * @property string $customs_signer
 * @property string $non_delivery_option
 * @property string $restriction_type
 * @property string $restriction_comments
 * @property CustomsItem[] $customs_items
 * @property string $created_at
 * @property string $updated_at
 * @property string $declaration
 */
class CustomsInfo extends EasypostResource
{
    /**
     * Retrieve a customs info.
     *
     * @param string $id
     * @param string $apiKey
     * @return mixed
     */
    public static function retrieve($id, $apiKey = null)
    {
        return self::retrieveResource(get_class(), $id, $apiKey);
    }

    /**
     * Create a customs info.
     *
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        if (!isset($params['customs_info']) || !is_array($params['customs_info'])) {
            $clone = $params;
            unset($params);
            $params['customs_info'] = $clone;
        }

        return self::createResource(get_class(), $params, $apiKey);
    }
}

<?php

namespace EasyPost;

/**
 * @package EasyPost
 * @property string $id
 * @property string $object
 * @property string $status
 * @property string $message
 * @property Address $address
 * @property array $tracking_codes
 * @property string $form_url
 * @property string $form_file_type
 * @property string $batch_id
 * @property string $created_at
 * @property string $updated_at
 */
class ScanForm extends EasypostResource
{
    /**
     * Retrieve a scanform.
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
     * Retrieve all scanforms.
     *
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     */
    public static function all($params = null, $apiKey = null)
    {
        return self::allResources(get_class(), $params, $apiKey);
    }

    /**
     * Create a scanform.
     *
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        return self::createResource(get_class(), $params, $apiKey);
    }
}

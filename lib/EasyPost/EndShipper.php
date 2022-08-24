<?php

namespace EasyPost;

/**
 * @package EasyPost
 * @property string $name
 * @property string $company
 * @property string $id
 * @property string $street1
 * @property string $street2
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $country
 * @property string $phone
 * @property string $email
 */

class EndShipper extends EasypostResource
{
    /**
     * Create an EndShipper object.
     *
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        $wrappedParams = [];
        $wrappedParams['address'] = $params;

        return self::createResource(get_class(), $wrappedParams, $apiKey);
    }

    /**
     * Retrieve an EndShipper object.
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
     * Retrieve all EndShipper objects.
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
     * Update (save) an EndShipper object.
     *
     * @return $this
     */
    public function save()
    {
        // We are passing the `Address` class here so that the request gets properly wrapped in the required object.
        return $this->saveResource('EasyPost\Address', false, 'put');
    }
}

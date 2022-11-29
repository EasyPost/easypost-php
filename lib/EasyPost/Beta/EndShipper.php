<?php

namespace EasyPost\Beta;

use EasyPost\EasypostResource;

/**
 * @package EasyPost\Beta
 * @property string $id
 * @property string $street1
 * @property string $street2
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $country
 * @property string $name
 * @property string $company
 * @property string $phone
 *
 * @deprecated Use EasyPost\EndShipper instead.
 */

class EndShipper extends EasypostResource
{
    /**
     * @deprecated Use EasyPost\EndShipper instead.
     * Create an EndShipper object.
     *
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        error_log('Method ' . __METHOD__ . ' is deprecated, use EasyPost\EndShipper instead');

        $wrappedParams = [];
        $wrappedParams['address'] = $params;

        return self::createResource(get_class(), $wrappedParams, $apiKey, null, true);
    }

    /**
     * @deprecated Use EasyPost\EndShipper instead.
     * Retrieve an EndShipper object.
     *
     * @param string $id
     * @param string $apiKey
     * @return mixed
     */
    public static function retrieve($id, $apiKey = null)
    {
        error_log('Method ' . __METHOD__ . ' is deprecated, use EasyPost\EndShipper instead');

        return self::retrieveResource(get_class(), $id, $apiKey, true);
    }

    /**
     * @deprecated Use EasyPost\EndShipper instead.
     * Retrieve all EndShipper objects.
     *
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     */
    public static function all($params = null, $apiKey = null)
    {
        error_log('Method ' . __METHOD__ . ' is deprecated, use EasyPost\EndShipper instead');

        return self::allResources(get_class(), $params, $apiKey, true);
    }

    /**
     * @deprecated Use EasyPost\EndShipper instead.
     * Update (save) an EndShipper object.
     *
     * @return $this
     */
    public function save()
    {
        error_log('Method ' . __METHOD__ . ' is deprecated, use EasyPost\EndShipper instead');

        // We are passing the `Address` class here so that the request gets properly wrapped in the required object.
        return $this->saveResource('EasyPost\Address', true, 'put');
    }
}

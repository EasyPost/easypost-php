<?php

namespace EasyPost;

/**
 * @package EasyPost
 * @property string $id
 * @property string $object
 * @property string $type
 * @property bool $clone
 * @property string $description
 * @property string $reference
 * @property string $readable
 * @property object $credentials
 * @property object $test_credentials
 * @property string $created_at
 * @property string $updated_at
 */
class CarrierAccount extends EasypostResource
{
    /**
     * Retrieve a carrier account.
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
     * Retrieve all carrier accounts.
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
     * Update (save) a carrier account.
     *
     * @return $this
     */
    public function save()
    {
        return $this->saveResource(get_class());
    }

    /**
     * Delete a carrier account.
     *
     * @return $this
     */
    public function delete()
    {
        return $this->deleteResource();
    }

    /**
     * Create a carrier account.
     *
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        if (!isset($params['carrier_account']) || !is_array($params['carrier_account'])) {
            $clone = $params;
            unset($params);
            $params['carrier_account'] = $clone;
        }
        return self::createResource(get_class(), $params, $apiKey);
    }

    /**
     * Get the types of carrier accounts available to the user.
     *
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     */
    public static function types($params = null, $apiKey = null)
    {
        $requestor = new Requestor($apiKey);
        list($response, $apiKey) = $requestor->request('get', '/carrier_types', $params);
        return Util::convertToEasyPostObject($response, $apiKey);
    }
}

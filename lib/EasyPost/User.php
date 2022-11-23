<?php

namespace EasyPost;

/**
 * @package EasyPost
 * @property string $id
 * @property string $object
 * @property string $parent_id
 * @property string $name
 * @property string $email
 * @property string $phone_number
 * @property string $balance
 * @property string $price_per_shipment
 * @property string $recharge_amount
 * @property string $secondary_recharge_amount
 * @property string $recharge_threshold
 * @property string $cc_fee_rate
 * @property string $insurance_fee_rate
 * @property string $insurance_fee_minimum
 * @property User[] $children
 */
class User extends EasypostResource
{
    /**
     * Retrieve a user.
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
     * Save (update) a user.
     *
     * @return $this
     */
    public function save()
    {
        return $this->saveResource(get_class());
    }

    /**
     * Create a child user.
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        if (!isset($params['user']) || !is_array($params['user'])) {
            $clone = $params;
            unset($params);
            $params['user'] = $clone;
        }
        return self::createResource(get_class(), $params, $apiKey, null);
    }

    /**
     * Retrieve me (the authenticated user).
     *
     * @param string $apiKey
     * @return mixed
     */
    public static function retrieve_me($apiKey = null)
    {
        return self::allResources(get_class(), null, $apiKey);
    }

    /**
     * Delete a user.
     *
     * @param string $apiKey
     * @return $this
     */
    public function delete($params = null, $apiKey = null)
    {
        return $this->deleteResource($params, true);
    }

    /**
     * Retrieve a list of all API keys.
     *
     * @param null $apiKey
     * @return object
     */
    public static function all_api_keys($apiKey = null)
    {
        $requestor = new Requestor($apiKey);
        list($response, $apiKey) = $requestor->request('get', '/api_keys');
        return Util::convertToEasyPostObject($response, $apiKey);
    }

    /**
     * Retrieve a list of API keys (works for the authenticated user or a child user).
     *
     * @param string $apiKey
     * @return array
     */
    public function api_keys()
    {
        $apiKeys = self::all_api_keys();

        if ($apiKeys->id == $this->id) {
            // This function was called on the authenticated user
            $myApiKeys = $apiKeys->keys;
        } else {
            // This function was called on a child user (authenticated as parent, only return this child user's details).
            $myApiKeys = [];
            foreach ($apiKeys->children as $childrenKeys) {
                if ($childrenKeys->id == $this->id) {
                    $myApiKeys = $childrenKeys->keys;
                    break;
                }
            }
        }

        // TODO: Don't rewrap objects here, return the direct response objects
        $result = [];
        foreach ($myApiKeys as $key) {
            $result[$key->mode] = $key->key;
        }

        return $result;
    }

    /**
     * Update the User's Brand object.
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return mixed
     */
    public function update_brand($params = null, $apiKey = null)
    {
        $requestor = new Requestor($apiKey);
        list($response, $apiKey) = $requestor->request('patch', $this->instanceUrl() . '/brand', $params);
        return Util::convertToEasyPostObject($response, $apiKey);
    }
}

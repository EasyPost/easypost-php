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
        return self::_retrieve(get_class(), $id, $apiKey);
    }

    /**
     * Save (update) a user.
     *
     * @return $this
     */
    public function save()
    {
        return $this->_save(get_class());
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
        return self::_create(get_class(), $params, $apiKey, null);
    }

    /**
     * Retrieve me (the authenticated user).
     *
     * @param string $apiKey
     * @return mixed
     */
    public static function retrieve_me($apiKey = null)
    {
        return self::_all(get_class(), null, $apiKey);
    }

    /**
     * Delete a user.
     *
     * @param string $apiKey
     * @return $this
     */
    public function delete($params = null, $apiKey = null)
    {
        return $this->_delete($params, true);
    }

    /**
     * Get all API keys including child user keys.
     *
     * @param null $apiKey
     * @return mixed
     */
    public static function all_api_keys($apiKey = null)
    {
        $requestor = new Requestor($apiKey);
        list($response, $apiKey) = $requestor->request('get', '/api_keys');
        return Util::convertToEasyPostObject($response, $apiKey);
    }

    /**
     * Get my API keys.
     *
     * @param string $apiKey
     * @return array|null
     */
    public function api_keys($apiKey = null)
    {
        $api_keys = self::all_api_keys();
        $my_api_keys = null;

        if ($api_keys->id == $this->id) {
            $my_api_keys = $api_keys->keys;
        }
        if (is_null($my_api_keys)) {
            foreach ($api_keys->children as $children_keys) {
                if ($children_keys->id == $this->id) {
                    $my_api_keys = $children_keys->keys;
                }
            }
        }

        if (is_null($my_api_keys)) {
            return null;
        } else {
            $response = [];
            foreach ($my_api_keys as $key) {
                $response[$key->mode] = $key->key;
            }
            return $response;
        }
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
        list($response, $apiKey) = $requestor->request('put', $this->instanceUrl() . '/brand', $params);
        return Util::convertToEasyPostObject($response, $apiKey);
    }
}

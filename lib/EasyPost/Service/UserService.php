<?php

namespace EasyPost\Service;

use EasyPost\Http\Requestor;
use EasyPost\Util\InternalUtil;

/**
 * User service containing all the logic to make API calls.
 */
class UserService extends BaseService
{
    /**
     * Retrieve a user.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve($id)
    {
        return self::retrieveResource(self::serviceModelClassName(self::class), $id);
    }

    /**
     * Update a user.
     *
     * @return mixed
     */
    public function update($id, $params)
    {
        if (!isset($params['user']) || !is_array($params['user'])) {
            $clone = $params;
            unset($params);
            $params['user'] = $clone;
        }

        return $this->updateResource(self::serviceModelClassName(self::class), $id, $params);
    }

    /**
     * Create a child user.
     *
     * @param mixed $params
     * @return mixed
     */
    public function create($params = null)
    {
        if (!isset($params['user']) || !is_array($params['user'])) {
            $clone = $params;
            unset($params);
            $params['user'] = $clone;
        }

        return self::createResource(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Retrieve the authenticated user.
     *
     * @return mixed
     */
    public function retrieveMe()
    {
        return self::allResources(self::serviceModelClassName(self::class));
    }

    /**
     * Delete a user.
     *
     * @param string $id
     * @param mixed @params
     * @return mixed
     */
    public function delete($id, $params = null)
    {
        return $this->deleteResource(self::serviceModelClassName(self::class), $id, $params);
    }

    /**
     * Retrieve a list of all API keys.
     *
     * @deprecated Use all() under the api_key service instead.
     *
     * @return mixed
     */
    public function allApiKeys()
    {
        $response = Requestor::request($this->client, 'get', '/api_keys');

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Retrieve a list of API keys (works for the authenticated user or a child user).
     *
     * @deprecated Use retrieve_api_key_for_user() under the api_key service instead.
     *
     * @param string $id
     * @return mixed
     */
    public function apiKeys($id)
    {
        $apiKeys = self::allApiKeys();

        if ($apiKeys->id == $id) {
            // This function was called on the authenticated user
            $myApiKeys = $apiKeys->keys;
        } else {
            // This function was called on a child user, authenticated as parent, only return this child user's details
            $myApiKeys = [];
            foreach ($apiKeys->children as $childrenKeys) {
                if ($childrenKeys->id == $id) {
                    $myApiKeys = $childrenKeys->keys;
                    break;
                }
            }
        }

        return $myApiKeys;
    }

    /**
     * Update the User's Brand object.
     *
     * @param string $id
     * @param mixed $params
     * @return mixed
     */
    public function updateBrand($id, $params = null)
    {
        $response = Requestor::request(
            $this->client,
            'patch',
            $this->instanceUrl(self::serviceModelClassName(self::class), $id) . '/brand',
            $params
        );

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }
}

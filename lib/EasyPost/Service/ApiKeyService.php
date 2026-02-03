<?php

namespace EasyPost\Service;

use EasyPost\Constant\Constants;
use EasyPost\Exception\General\FilteringException;
use EasyPost\Http\Requestor;
use EasyPost\Util\InternalUtil;

/**
 * API key service containing all the logic to make API calls.
 */
class ApiKeyService extends BaseService
{
    /**
     * Retrieve a list of API keys (works for the authenticated user or a child user).
     *
     * @param string $id The user ID to retrieve API keys for
     * @return mixed
     * @throws FilteringException If no user is found with the given ID
     */
    public function retrieveApiKeysForUser(string $id): mixed
    {
        $apiKeys = self::all();

        if ($apiKeys->id == $id) {
            // This function was called on the authenticated user
            return $apiKeys->keys;
        }

        // This function was called on a child user, authenticated as parent, only return this child user's details
        foreach ($apiKeys->children as $childrenKeys) {
            if ($childrenKeys->id == $id) {
                return $childrenKeys->keys;
            }
        }

        throw new FilteringException(Constants::NO_USER_FOUND_ERROR);
    }

    /**
     * Retrieve a list of all API keys.
     *
     * @return mixed
     */
    public function all(): mixed
    {
        $response = Requestor::request($this->client, 'get', '/api_keys');

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Create an API key for a child or referral customer user.
     *
     * @param string $mode
     * @return mixed
     */
    public function create(string $mode): mixed
    {
        $params = ['mode' => $mode];

        return self::createResource(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Delete an API key for a child or referral customer user.
     *
     * @param string $id
     * @return void
     */
    public function delete(string $id): void
    {
        $this->deleteResource(self::serviceModelClassName(self::class), $id);
    }

    /**
     * Enable a child or referral customer API key.
     *
     * @param string $id
     * @return mixed
     */
    public function enable(string $id): mixed
    {
        $response = Requestor::request($this->client, 'post', "/api_keys/{$id}/enable");

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Disable a child or referral customer API key.
     *
     * @param string $id
     * @return mixed
     */
    public function disable(string $id): mixed
    {
        $response = Requestor::request($this->client, 'post', "/api_keys/{$id}/disable");

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }
}

<?php

namespace EasyPost\Service;

use EasyPost\Http\Requestor;
use EasyPost\Util\InternalUtil;

/**
 * API key service containing all the logic to make API calls.
 */
class ApiKeyService extends BaseService
{
    /**
     * Retrieve a list of all API keys.
     *
     * @return mixed
     */
    public function all()
    {
        $response = Requestor::request($this->client, 'get', '/api_keys');

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Retrieve a list of API keys (works for the authenticated user or a child user).
     *
     * @param string $id The user ID to retrieve API keys for
     * @return mixed
     */
    public function retrieveApiKeysForUser($id)
    {
        $apiKeys = self::all();

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
}

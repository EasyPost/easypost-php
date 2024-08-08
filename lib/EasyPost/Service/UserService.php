<?php

namespace EasyPost\Service;

use EasyPost\Exception\General\EndOfPaginationException;
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
    public function retrieve(string $id): mixed
    {
        return self::retrieveResource(self::serviceModelClassName(self::class), $id);
    }

    /**
     * Update a user.
     *
     * @param string $id
     * @param mixed $params
     * @return mixed
     */
    public function update(string $id, mixed $params): mixed
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
    public function create(mixed $params = null): mixed
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
    public function retrieveMe(): mixed
    {
        return self::allResources(self::serviceModelClassName(self::class));
    }

    /**
     * Delete a user.
     *
     * @param string $id
     * @param mixed $params
     * @return void
     */
    public function delete(string $id, mixed $params = null): void
    {
        $this->deleteResource(self::serviceModelClassName(self::class), $id, $params);
    }

    /**
     * Retrieve a list of all API keys.
     *
     * @deprecated Use all() under the api_key service instead.
     *
     * @return mixed
     */
    public function allApiKeys(): mixed
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
    public function apiKeys(string $id): mixed
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
    public function updateBrand(string $id, mixed $params = null): mixed
    {
        $response = Requestor::request(
            $this->client,
            'patch',
            $this->instanceUrl(self::serviceModelClassName(self::class), $id) . '/brand',
            $params
        );

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Retrieve all child users.
     *
     * @param mixed $params
     * @return mixed
     */
    public function allChildren(mixed $params = null): mixed
    {
        self::validate($params);

        $url = '/users/children';
        $response = Requestor::request($this->client, 'get', $url, $params);
        if (isset($params)) {
            $response['_params'] = $params;
        }

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Retrieve the next page of child User collection
     *
     * @param mixed $children
     * @param int|null $pageSize
     * @return mixed
     */
    public function getNextPageOfChildren(mixed $children, ?int $pageSize = null): mixed
    {
        $userArray = $children['children'];
        $userParams = $children['_params'] ?? null;

        // Check to see if there is another page server-side before attempting to retrieve it
        if (empty($userArray) || !$children['has_more']) {
            throw new EndOfPaginationException();
        }

        $params = [
            'page_size' => $pageSize,
            'after_id' => $userArray[count($userArray) - 1]['id']
        ];

        if (isset($userParams)) {
            $params = array_merge($params, $userParams);
        }

        // Retrieve a page of users from the server
        $children = $this->allChildren($params);

        // If the page we just retrieved is empty, then we have reached the end of the list
        if (empty($children['children'])) {
            throw new EndOfPaginationException();
        }

        return $children;
    }
}

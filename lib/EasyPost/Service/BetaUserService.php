<?php

namespace EasyPost\Service;

use EasyPost\Http\Requestor;
use EasyPost\Util\InternalUtil;
use EasyPost\Exception\General\EndOfPaginationException;

/**
 * User service containing all the logic to make API calls.
 */
class BetaUserService extends BaseService
{
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
        $response = Requestor::request($this->client, 'get', $url, $params, true);
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
            'before_id' => $userArray[count($userArray) - 1]['id']
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

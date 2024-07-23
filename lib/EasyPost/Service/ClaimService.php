<?php

namespace EasyPost\Service;

use EasyPost\Http\Requestor;
use EasyPost\Util\InternalUtil;

/**
 * Claim service containing all the logic to make API calls.
 */
class ClaimService extends BaseService
{
    /**
     * Create an claim object.
     *
     * @param mixed $params
     * @return mixed
     */
    public function create(mixed $params = null): mixed
    {
        return self::createResource(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Retrieve an claim object.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve(string $id): mixed
    {
        return self::retrieveResource(self::serviceModelClassName(self::class), $id);
    }

    /**
     * Retrieve all claim objects.
     *
     * @param mixed $params
     * @return mixed
     */
    public function all(mixed $params = null): mixed
    {
        return self::allResources(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Retrieve the next page of claim collection.
     *
     * @param mixed $claims
     * @param int|null $pageSize
     * @return mixed
     */
    public function getNextPage(mixed $claims, ?int $pageSize = null): mixed
    {
        return $this->getNextPageResources(self::serviceModelClassName(self::class), $claims, $pageSize);
    }

    /**
     * Cancel a claim object.
     *
     * @param string $id
     * @return mixed
     */
    public function cancel(string $id): mixed
    {
        $response = Requestor::request($this->client, 'post', "/claims/{$id}/cancel");

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }
}

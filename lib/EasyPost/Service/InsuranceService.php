<?php

namespace EasyPost\Service;

use EasyPost\Http\Requestor;
use EasyPost\Util\InternalUtil;

/**
 * Insurance service containing all the logic to make API calls.
 */
class InsuranceService extends BaseService
{
    /**
     * Retrieve an insurance object.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve(string $id): mixed
    {
        return self::retrieveResource(self::serviceModelClassName(self::class), $id);
    }

    /**
     * Retrieve all insurance objects.
     *
     * @param mixed $params
     * @return mixed
     */
    public function all(mixed $params = null): mixed
    {
        return self::allResources(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Retrieve the next page of Insurance collection
     *
     * @param mixed $insurances
     * @param int|null $pageSize
     * @return mixed
     */
    public function getNextPage(mixed $insurances, ?int $pageSize = null): mixed
    {
        return $this->getNextPageResources(self::serviceModelClassName(self::class), $insurances, $pageSize);
    }

    /**
     * Create an insurance object.
     *
     * @param mixed $params
     * @return mixed
     */
    public function create(mixed $params = null): mixed
    {
        $params = InternalUtil::wrapParams($params, 'insurance');

        return self::createResource(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Refund an insurance object.
     *
     * @param string $id
     * @return mixed
     */
    public function refund(string $id): mixed
    {
        $response = Requestor::request($this->client, 'post', "/insurances/{$id}/refund");

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }
}

<?php

namespace EasyPost\Service;

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
        if (!isset($params['insurance']) || !is_array($params['insurance'])) {
            $clone = $params;
            unset($params);
            $params['insurance'] = $clone;
        }

        return self::createResource(self::serviceModelClassName(self::class), $params);
    }
}

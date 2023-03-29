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
    public function retrieve($id)
    {
        return self::retrieveResource(self::serviceModelClassName(self::class), $id);
    }

    /**
     * Retrieve all insurance objects.
     *
     * @param mixed $params
     * @return mixed
     */
    public function all($params = null)
    {
        return self::allResources(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Retrieve the next page of Insurance collection
     *
     * @param mixed $insurances
     * @param string $pageSize
     * @return mixed
     */
    public function getNextPage($insurances, $pageSize = null)
    {
        return $this->getNextPageResources(self::serviceModelClassName(self::class), $insurances, $pageSize);
    }

    /**
     * Create an insurance object.
     *
     * @param mixed $params
     * @return mixed
     */
    public function create($params = null)
    {
        if (!isset($params['insurance']) || !is_array($params['insurance'])) {
            $clone = $params;
            unset($params);
            $params['insurance'] = $clone;
        }

        return self::createResource(self::serviceModelClassName(self::class), $params);
    }
}

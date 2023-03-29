<?php

namespace EasyPost\Service;

/**
 * Report service containing all the logic to make API calls.
 */
class ScanFormService extends BaseService
{
    /**
     * Retrieve a scanform.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve($id)
    {
        return self::retrieveResource(self::serviceModelClassName(self::class), $id);
    }

    /**
     * Retrieve all scanforms.
     *
     * @param mixed $params
     * @return mixed
     */
    public function all($params = null)
    {
        return self::allResources(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Retrieve the next page of ScanForm collection
     *
     * @param mixed $scanforms
     * @param string $pageSize
     * @return mixed
     */
    public function getNextPage($scanforms, $pageSize = null)
    {
        return $this->getNextPageResources(self::serviceModelClassName(self::class), $scanforms, $pageSize);
    }

    /**
     * Create a scanform.
     *
     * @param mixed $params
     * @return mixed
     */
    public function create($params = null)
    {
        return self::createResource(self::serviceModelClassName(self::class), $params);
    }
}

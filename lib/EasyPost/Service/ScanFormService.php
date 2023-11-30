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
    public function retrieve(string $id): mixed
    {
        return self::retrieveResource(self::serviceModelClassName(self::class), $id);
    }

    /**
     * Retrieve all scanforms.
     *
     * @param mixed $params
     * @return mixed
     */
    public function all(mixed $params = null): mixed
    {
        return self::allResources(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Retrieve the next page of ScanForm collection
     *
     * @param mixed $scanforms
     * @param int|null $pageSize
     * @return mixed
     */
    public function getNextPage(mixed $scanforms, ?int $pageSize = null): mixed
    {
        return $this->getNextPageResources(self::serviceModelClassName(self::class), $scanforms, $pageSize);
    }

    /**
     * Create a scanform.
     *
     * @param mixed $params
     * @return mixed
     */
    public function create(mixed $params = null): mixed
    {
        return self::createResource(self::serviceModelClassName(self::class), $params);
    }
}

<?php

namespace EasyPost\Service;

/**
 * Refund service containing all the logic to make API calls.
 */
class RefundService extends BaseService
{
    /**
     * Retrieve a refund.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve($id)
    {
        return self::retrieveResource(self::serviceModelClassName(self::class), $id);
    }

    /**
     * Retrieve all refunds.
     *
     * @param mixed $params
     * @return mixed
     */
    public function all($params = null)
    {
        return self::allResources(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Retrieve the next page of Refund collection
     *
     * @param mixed $refunds
     * @param string $pageSize
     * @return mixed
     */
    public function getNextPage($refunds, $pageSize = null)
    {
        return $this->getNextPageResources(self::serviceModelClassName(self::class), $refunds, $pageSize);
    }

    /**
     * Create a refund.
     *
     * @param mixed $params
     * @return mixed
     */
    public function create($params = null)
    {
        if (!isset($params['refund']) || !is_array($params['refund'])) {
            $clone = $params;
            unset($params);
            $params['refund'] = $clone;
        }

        return self::createResource(self::serviceModelClassName(self::class), $params);
    }
}

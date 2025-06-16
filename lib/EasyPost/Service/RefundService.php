<?php

namespace EasyPost\Service;

use EasyPost\Util\InternalUtil;

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
    public function retrieve(string $id): mixed
    {
        return self::retrieveResource(self::serviceModelClassName(self::class), $id);
    }

    /**
     * Retrieve all refunds.
     *
     * @param mixed $params
     * @return mixed
     */
    public function all(mixed $params = null): mixed
    {
        return self::allResources(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Retrieve the next page of Refund collection
     *
     * @param mixed $refunds
     * @param ?int $pageSize
     * @return mixed
     */
    public function getNextPage(mixed $refunds, ?int $pageSize = null): mixed
    {
        return $this->getNextPageResources(self::serviceModelClassName(self::class), $refunds, $pageSize);
    }

    /**
     * Create a refund.
     *
     * @param mixed $params
     * @return mixed
     */
    public function create(mixed $params = null): mixed
    {
        $params = InternalUtil::wrapParams($params, 'refund');

        return self::createResource(self::serviceModelClassName(self::class), $params);
    }
}

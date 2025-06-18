<?php

namespace EasyPost\Service;

use EasyPost\Http\Requestor;
use EasyPost\Util\InternalUtil;

/**
 * Tracker service containing all the logic to make API calls.
 */
class TrackerService extends BaseService
{
    /**
     * Retrieve a tracker.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve(string $id): mixed
    {
        return self::retrieveResource(self::serviceModelClassName(self::class), $id);
    }

    /**
     * Retrieve all trackers.
     *
     * @param mixed $params
     * @return mixed
     */
    public function all(mixed $params = null): mixed
    {
        return self::allResources(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Retrieve the next page of Tracker collection
     *
     * @param mixed $trackers
     * @param int|null $pageSize
     * @return mixed
     */
    public function getNextPage(mixed $trackers, ?int $pageSize = null): mixed
    {
        return $this->getNextPageResources(self::serviceModelClassName(self::class), $trackers, $pageSize);
    }

    /**
     * Create a tracker.
     *
     * @param mixed $params
     * @return mixed
     */
    public function create(mixed $params = null): mixed
    {
        // TODO: only accept wrapped params
        if (!is_array($params)) {
            $clone = $params;
            unset($params);
            $params['tracker']['tracking_code'] = $clone;
        } elseif (!isset($params['tracker']) || !is_array($params['tracker'])) {
            $clone = $params;
            unset($params);
            $params['tracker'] = $clone;
        }

        return self::createResource(self::serviceModelClassName(self::class), $params);
    }
}

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
    public function retrieve($id)
    {
        return self::retrieveResource(self::serviceModelClassName(self::class), $id);
    }

    /**
     * Retrieve all trackers.
     *
     * @param mixed $params
     * @return mixed
     */
    public function all($params = null)
    {
        self::validate($params);
        $response = Requestor::request($this->client, 'get', '/trackers', $params);
        $response['tracking_code'] = $params['tracking_code'] ?? null;
        $response['carrier'] = $params['carrier'] ?? null;

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Retrieve the next page of Tracker collection
     *
     * @param mixed $trackers
     * @param string $pageSize
     * @return mixed
     */
    public function getNextPage($trackers, $pageSize = null)
    {
        $params = [];

        if (isset($trackers->tracking_code)) {
            $params['tracking_code'] = $trackers->tracking_code;
        }

        if (isset($trackers->carrier)) {
            $params['carrier'] = $trackers->carrier;
        }
        return $this->getNextPageResources(self::serviceModelClassName(self::class), $trackers, $pageSize, $params);
    }

    /**
     * Create a tracker.
     *
     * @param mixed $params
     * @return mixed
     */
    public function create($params = null)
    {
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

    /**
     * Create a list of trackers.
     *
     * @param mixed $params
     * @return void
     */
    public function createList($params = null)
    {
        if (!isset($params['trackers']) || !is_array($params['trackers'])) {
            $clone = $params;
            unset($params);
            $params = ['trackers' => $clone];
        }

        $url = self::classUrl(self::serviceModelClassName(self::class));

        Requestor::request($this->client, 'post', $url . '/create_list', $params);
    }
}

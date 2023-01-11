<?php

namespace EasyPost\Service;

use EasyPost\Http\Requestor;

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
        return self::allResources(self::serviceModelClassName(self::class), $params);
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

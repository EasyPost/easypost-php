<?php

namespace EasyPost\Service;

use EasyPost\Http\Requestor;
use EasyPost\Util\InternalUtil;

/**
 * Event service containing all the logic to make API calls.
 */
class EventService extends BaseService
{
    /**
     * Retrieve an event.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve($id)
    {
        return self::retrieveResource(self::serviceModelClassName(self::class), $id);
    }

    /**
     * Retrieve all events.
     *
     * @param mixed $params
     * @return mixed
     */
    public function all($params = null)
    {
        return self::allResources(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Retrieve the next page of Event collection
     *
     * @param mixed $events
     * @param string $pageSize
     * @return mixed
     */
    public function getNextPage($events, $pageSize = null)
    {
        return $this->getNextPageResources(self::serviceModelClassName(self::class), $events, $pageSize);
    }

    /**
     * Retrieve all payloads for an event.
     *
     * @param string $id The event id
     * @return mixed
     */
    public function retrieveAllPayloads($id)
    {
        $url = $this->instanceUrl(self::serviceModelClassName(self::class), $id) . '/payloads';

        $response = Requestor::request($this->client, 'get', $url);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Retrieve a payload for an event.
     *
     * @param string $id The event id
     * @param string $payloadId The payload id
     * @return mixed
     */
    public function retrievePayload($id, $payloadId)
    {
        $url = $this->instanceUrl(self::serviceModelClassName(self::class), $id) . '/payloads/' . $payloadId;

        $response = Requestor::request($this->client, 'get', $url);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }
}

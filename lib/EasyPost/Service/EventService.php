<?php

namespace EasyPost\Service;

/**
 * Event service containing all the logic to make API calls.
 */
class EventService extends BaseService
{
    private static $modelClass = 'Event';

    /**
     * Retrieve an event.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve($id)
    {
        return self::retrieveResource(self::$modelClass, $id);
    }

    /**
     * Retrieve all events.
     *
     * @param mixed $params
     * @return mixed
     */
    public function all($params = null)
    {
        return self::allResources(self::$modelClass, $params);
    }
}

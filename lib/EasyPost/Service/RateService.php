<?php

namespace EasyPost\Service;

/**
 * Rate service containing all the logic to make API calls.
 */
class RateService extends BaseService
{
    /**
     * Retrieve a rate.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve($id)
    {
        return self::retrieveResource(self::serviceModelClassName(self::class), $id);
    }
}

<?php

namespace EasyPost\Service;

use EasyPost\Http\Requestor;
use EasyPost\Util\InternalUtil;

/**
 * Luma service containing all the logic to make API calls.
 */
class LumaService extends BaseService
{
    /**
     * Get service recommendations from Luma that meet the criteria of your ruleset.
     *
     * @param array<mixed> $params
     * @return mixed
     */
    public function getPromise(array $params): mixed
    {
        $params = InternalUtil::wrapParams($params, 'shipment');

        $url = '/luma/promise';
        $response = Requestor::request($this->client, 'post', $url, $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response['luma_info']);
    }
}

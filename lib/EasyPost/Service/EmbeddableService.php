<?php

namespace EasyPost\Service;

use EasyPost\Http\HttpMethod;
use EasyPost\Http\Requestor;
use EasyPost\Util\InternalUtil;

/**
 * Embeddable service containing all the logic to make API calls.
 */
class EmbeddableService extends BaseService
{
    /**
     * Create an Embeddable Session.
     *
     * @param mixed $params
     * @return mixed
     */
    public function createSession(mixed $params = null): mixed
    {
        $response = Requestor::request($this->client, HttpMethod::POST, '/embeddables/session', $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }
}

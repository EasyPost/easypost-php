<?php

namespace EasyPost\Service;

use EasyPost\Http\Requestor;
use EasyPost\Util\InternalUtil;

/**
 * CarrierMetadata service containing all the logic to make API calls.
 */
class CarrierMetadataService extends BaseService
{
    /**
     * Get carrier metadata for all carriers on the EasyPost platform.
     *
     * @param array<string>|null $carriers
     * @param array<string>|null $types
     * @return mixed
     */
    public function retrieve(?array $carriers = null, ?array $types = null): mixed
    {
        $url = '/metadata/carriers';
        if (isset($carriers) || isset($types)) {
            $url = "{$url}?";
        }
        if (isset($carriers)) {
            $url = "{$url}carriers=" . join(',', $carriers);
        }
        if (isset($carriers) && isset($types)) {
            $url = "{$url}&";
        }
        if (isset($types)) {
            $url = "{$url}types=" . join(',', $types);
        }

        $response = Requestor::request($this->client, 'get', $url, null);

        return InternalUtil::convertToEasyPostObject($this->client, $response['carriers'] ?? []);
    }
}

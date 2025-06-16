<?php

namespace EasyPost\Service;

use EasyPost\Http\Requestor;
use EasyPost\Util\InternalUtil;

/**
 * Address service containing all the logic to make API calls.
 */
class AddressService extends BaseService
{
    /**
     * Retrieve an address.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve(string $id): mixed
    {
        return self::retrieveResource(self::serviceModelClassName(self::class), $id);
    }

    /**
     * Retrieve all addresses.
     *
     * @param mixed $params
     * @return mixed
     */
    public function all(mixed $params = null): mixed
    {
        return self::allResources(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Retrieve the next page of Address collection
     *
     * @param mixed $addresses
     * @param int|null $pageSize
     * @return mixed
     */
    public function getNextPage(mixed $addresses, ?int $pageSize = null)
    {
        return $this->getNextPageResources(self::serviceModelClassName(self::class), $addresses, $pageSize);
    }

    /**
     * Create an address.
     *
     * @param mixed $params
     * @return mixed
     */
    public function create(mixed $params = null): mixed
    {
        $wrappedParams = [];

        if (isset($params['verify'])) {
            $verify = $params['verify'];
            unset($params['verify']);
            $wrappedParams['verify'] = $verify;
        }

        if (isset($params['verify_strict'])) {
            $verifyStrict = $params['verify_strict'];
            unset($params['verify_strict']);
            $wrappedParams['verify_strict'] = $verifyStrict;
        }

        $wrappedParams['address'] = $params;

        return self::createResource(self::serviceModelClassName(self::class), $wrappedParams);
    }

    /**
     * Create and verify an address.
     *
     * @param mixed $params
     * @return mixed
     */
    public function createAndVerify(mixed $params = null): mixed
    {
        $params = InternalUtil::wrapParams($params, 'address');

        $url = self::classUrl(self::serviceModelClassName(self::class));
        $response = Requestor::request($this->client, 'post', $url . '/create_and_verify', $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response['address']);
    }

    /**
     * Verify an address.
     *
     * @param string $id
     * @return mixed
     */
    public function verify(string $id): mixed
    {
        $url = $this->instanceUrl(self::serviceModelClassName(self::class), $id) . '/verify';
        $response = Requestor::request($this->client, 'get', $url, null);

        return InternalUtil::convertToEasyPostObject($this->client, $response['address']);
    }
}

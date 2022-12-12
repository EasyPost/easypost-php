<?php

namespace EasyPost\Service;

use EasyPost\Constant\Constants;
use EasyPost\Exception\Error;
use EasyPost\Http\Requestor;
use EasyPost\Util\InternalUtil;

/**
 * CarrierAccount service containing all the logic to make API calls.
 */
class CarrierAccountService extends BaseService
{
    private static $modelClass = 'CarrierAccount';

    /**
     * Retrieve a carrier account.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve($id)
    {
        return self::retrieveResource(self::$modelClass, $id);
    }

    /**
     * Retrieve all carrier accounts.
     *
     * @param mixed $params
     * @return mixed
     */
    public function all($params = null)
    {
        return self::allResources(self::$modelClass, $params);
    }

    /**
     * Update a carrier account.
     *
     * @param string $id
     * @param mixed $params
     * @return mixed
     */
    public function update($id, $params)
    {
        return self::updateResource(self::$modelClass, $id, $params);
    }

    /**
     * Delete a carrier account.
     *
     * @param string $id
     * @param mixed $params
     * @return void
     */
    public function delete($id, $params = null)
    {
        self::deleteResource(self::$modelClass, $id, $params);
    }

    /**
     * Create a carrier account.
     *
     * @param mixed $params
     * @return mixed
     * @throws Error
     */
    public function create($params = null)
    {
        if (!isset($params['carrier_account']) || !is_array($params['carrier_account'])) {
            $clone = $params;
            unset($params);
            $params['carrier_account'] = $clone;
        }

        $type = $params['carrier_account']['type'];
        if (!isset($type)) {
            throw new Error('type is required');
        }

        $url = self::selectCarrierAccountCreationEndpoint($type);

        $requestor = new Requestor($this->client);
        $response = $requestor->request('post', $url, $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Get the types of carrier accounts available to the user.
     *
     * @param mixed $params
     * @return mixed
     */
    public function types($params = null)
    {
        $requestor = new Requestor($this->client);
        $response = $requestor->request('get', '/carrier_types', $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Select the endpoint for creating a carrier account based on the type of carrier account.
     *
     * @param string $carrierAccountType The type of carrier account to create.
     * @return string The endpoint for creating a carrier account.
     */
    private function selectCarrierAccountCreationEndpoint($carrierAccountType): string
    {
        if (in_array($carrierAccountType, Constants::CARRIER_ACCOUNT_TYPES_WITH_CUSTOM_WORKFLOWS, true)) {
            return '/carrier_accounts/register';
        }

        return '/carrier_accounts';
    }
}

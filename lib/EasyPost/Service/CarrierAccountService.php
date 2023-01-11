<?php

namespace EasyPost\Service;

use EasyPost\Constant\Constants;
use EasyPost\Exception\General\MissingParameterException;
use EasyPost\Http\Requestor;
use EasyPost\Util\InternalUtil;

/**
 * CarrierAccount service containing all the logic to make API calls.
 */
class CarrierAccountService extends BaseService
{
    /**
     * Retrieve a carrier account.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve($id)
    {
        return self::retrieveResource(self::serviceModelClassName(self::class), $id);
    }

    /**
     * Retrieve all carrier accounts.
     *
     * @param mixed $params
     * @return mixed
     */
    public function all($params = null)
    {
        return self::allResources(self::serviceModelClassName(self::class), $params);
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
        return self::updateResource(self::serviceModelClassName(self::class), $id, $params);
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
        self::deleteResource(self::serviceModelClassName(self::class), $id, $params);
    }

    /**
     * Create a carrier account.
     *
     * @param mixed $params
     * @return mixed
     * @throws MissingParameterException
     */
    public function create($params = null)
    {
        if (!isset($params['carrier_account']) || !is_array($params['carrier_account'])) {
            $clone = $params;
            unset($params);
            $params['carrier_account'] = $clone;
        }

        if (!isset($params['carrier_account']['type'])) {
            throw new MissingParameterException(sprintf(Constants::MISSING_PARAMETER_ERROR, 'type'));
        }

        $url = self::selectCarrierAccountCreationEndpoint($params['carrier_account']['type']);
        $response = Requestor::request($this->client, 'post', $url, $params);

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
        $response = Requestor::request($this->client, 'get', '/carrier_types', $params);

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

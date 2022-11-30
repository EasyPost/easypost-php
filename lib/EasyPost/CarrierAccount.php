<?php

namespace EasyPost;

const CARRIER_ACCOUNT_TYPES_WITH_CUSTOM_WORKFLOWS = [
    'FedexAccount',
    'UpsAccount'
];

/**
 * @package EasyPost
 * @property string $id
 * @property string $object
 * @property string $type
 * @property bool $clone
 * @property string $description
 * @property string $reference
 * @property string $readable
 * @property object $credentials
 * @property object $test_credentials
 * @property string $created_at
 * @property string $updated_at
 */
class CarrierAccount extends EasypostResource
{
    /**
     * Retrieve a carrier account.
     *
     * @param string $id
     * @param string $apiKey
     * @return mixed
     */
    public static function retrieve($id, $apiKey = null)
    {
        return self::retrieveResource(get_class(), $id, $apiKey);
    }

    /**
     * Retrieve all carrier accounts.
     *
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     */
    public static function all($params = null, $apiKey = null)
    {
        return self::allResources(get_class(), $params, $apiKey);
    }

    /**
     * Update (save) a carrier account.
     *
     * @return $this
     */
    public function save()
    {
        return $this->saveResource(get_class());
    }

    /**
     * Delete a carrier account.
     *
     * @return $this
     */
    public function delete()
    {
        return $this->deleteResource();
    }

    /**
     * Create a carrier account.
     *
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     * @throws Error
     */
    public static function create($params = null, $apiKey = null)
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

        $requestor = new Requestor($apiKey);
        list($response, $apiKey) = $requestor->request('post', $url, $params);

        return Util::convertToEasyPostObject($response, $apiKey);
    }

    /**
     * Get the types of carrier accounts available to the user.
     *
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     */
    public static function types($params = null, $apiKey = null)
    {
        $requestor = new Requestor($apiKey);
        list($response, $apiKey) = $requestor->request('get', '/carrier_types', $params);
        return Util::convertToEasyPostObject($response, $apiKey);
    }

    /**
     * Select the endpoint for creating a carrier account based on the type of carrier account.
     *
     * @param string $carrierAccountType The type of carrier account to create.
     * @return string The endpoint for creating a carrier account.
     */
    private static function selectCarrierAccountCreationEndpoint($carrierAccountType): string
    {
        if (in_array($carrierAccountType, CARRIER_ACCOUNT_TYPES_WITH_CUSTOM_WORKFLOWS, true)) {
            return '/carrier_accounts/register';
        }
        return '/carrier_accounts';
    }
}

<?php

namespace EasyPost\Service;

use EasyPost\Http\HttpMethod;
use EasyPost\EasyPostClient;
use EasyPost\Http\Requestor;
use EasyPost\Util\InternalUtil;

/**
 * ReferralCustomer service containing all the logic to make API calls.
 */
class ReferralCustomerService extends BaseService
{
    /**
     * Retrieve all referrals.
     *
     * @param mixed $params
     * @return mixed
     */
    public function all(mixed $params = null): mixed
    {
        return self::allResources(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Retrieve the next page of Referral collection
     *
     * @param mixed $referrals
     * @param int|null $pageSize
     * @return mixed
     */
    public function getNextPage(mixed $referrals, ?int $pageSize = null): mixed
    {
        return $this->getNextPageResources(self::serviceModelClassName(self::class), $referrals, $pageSize);
    }

    /**
     * Create an referral.
     *
     * @param mixed $params
     * @return mixed
     */
    public function create(mixed $params = null): mixed
    {
        $params = InternalUtil::wrapParams($params, 'user');

        return self::createResource(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Update an referral email.
     *
     * @param string $userId
     * @param string $email
     * @return void
     */
    public function updateEmail(string $userId, string $email): void
    {
        $wrappedParams = [
            'user' => [
                'email' => $email
            ]
        ];

        Requestor::request($this->client, HttpMethod::PUT, "/referral_customers/{$userId}", $wrappedParams);
    }

    /**
     * Add a credit card to EasyPost for a ReferralCustomer with a payment method ID from Stripe.
     *
     * This function requires the ReferralCustomer User's API key.
     *
     * @param string $referralApiKey
     * @param string $paymentMethodId
     * @param string $priority
     * @return mixed
     */
    public function addCreditCardFromStripe(
        string $referralApiKey,
        string $paymentMethodId,
        string $priority = 'primary'
    ): mixed {
        $params = [
            'credit_card' => [
                'payment_method_id' => $paymentMethodId,
                'priority' => $priority,
            ]
        ];

        $client = new EasyPostClient($referralApiKey);
        $response = Requestor::request($client, HttpMethod::POST, '/credit_cards', $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Add a bank account to EasyPost for a ReferralCustomer.
     *
     * This function requires the ReferralCustomer User's API key.
     *
     * @param string $referralApiKey
     * @param string $financialConnectionsId
     * @param array<mixed> $mandateData
     * @param string $priority
     * @return mixed
     */
    public function addBankAccountFromStripe(
        string $referralApiKey,
        string $financialConnectionsId,
        array $mandateData,
        string $priority = 'primary'
    ): mixed {
        $params = [
            'financial_connections_id' => $financialConnectionsId,
            'mandate_data' => $mandateData,
            'priority' => $priority,
        ];

        $client = new EasyPostClient($referralApiKey);
        $response = Requestor::request($client, HttpMethod::POST, '/bank_accounts', $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Retrieves the public EasyPost Stripe API key.
     *
     * @return string
     */
    public function retrieveEasypostStripeApiKey(): string
    {
        $response = Requestor::request($this->client, HttpMethod::GET, '/partners/stripe_public_key');

        return $response['public_key'] ?? '';
    }
}

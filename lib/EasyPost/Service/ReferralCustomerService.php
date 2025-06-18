<?php

namespace EasyPost\Service;

use EasyPost\Constant\Constants;
use EasyPost\EasyPostClient;
use EasyPost\Exception\Api\ExternalApiException;
use EasyPost\Exception\Api\HttpException;
use EasyPost\Exception\Api\TimeoutException;
use EasyPost\Http\Requestor;
use EasyPost\Util\InternalUtil;
use GuzzleHttp\Client;

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

        Requestor::request($this->client, 'put', "/referral_customers/{$userId}", $wrappedParams);
    }

    /**
     * Add a credit card to EasyPost for a ReferralCustomer without needing a Stripe account.
     *
     * This function requires the Referral User's API key.
     *
     * @param string $referralApiKey
     * @param string $number
     * @param int $expirationMonth
     * @param int $expirationYear
     * @param string $cvc
     * @param string $priority
     * @return mixed
     * @throws ExternalApiException
     */
    public function addCreditCard(
        string $referralApiKey,
        string $number,
        int $expirationMonth,
        int $expirationYear,
        string $cvc,
        string $priority = 'primary'
    ): mixed {
        $easypostStripeApiKey = self::retrieveEasypostStripeApiKey();

        try {
            $stripeToken = self::createStripeToken(
                $number,
                $expirationMonth,
                $expirationYear,
                $cvc,
                $easypostStripeApiKey
            );
        } catch (\Exception $error) {
            throw new ExternalApiException(Constants::SEND_STRIPE_DETAILS_ERROR);
        }

        $stripeToken = $stripeToken['id'] ?? '';

        $response = self::createEasypostCreditCard($referralApiKey, $stripeToken, $priority);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
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
        $response = Requestor::request($client, 'post', '/credit_cards', $params);

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
        $response = Requestor::request($client, 'post', '/bank_accounts', $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Retrieves the public EasyPost Stripe API key.
     *
     * @return string
     */
    private function retrieveEasypostStripeApiKey(): string
    {
        $response = Requestor::request($this->client, 'get', '/partners/stripe_public_key');

        return $response['public_key'] ?? '';
    }

    /**
     * Retrieves the public EasyPost Stripe API key.
     *
     * @param string $number
     * @param int $expirationMonth
     * @param int $expirationYear
     * @param string $cvc
     * @param string $easypostStripeKey
     * @return mixed
     * @throws HttpException
     * @throws TimeoutException
     */
    private function createStripeToken(
        string $number,
        int $expirationMonth,
        int $expirationYear,
        string $cvc,
        string $easypostStripeKey
    ): mixed {
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => "Bearer $easypostStripeKey",
        ];

        $creditCardDetails = [
            'card' => [
                'number' => $number,
                'exp_month' => $expirationMonth,
                'exp_year' => $expirationYear,
                'cvc' => $cvc,
            ]
        ];

        $url = 'https://api.stripe.com/v1/tokens';

        $guzzleClient = new Client();

        $requestOptions['query'] = $creditCardDetails;
        $requestOptions['headers'] = $headers;
        $requestOptions['http_errors'] = false;

        try {
            $response = $guzzleClient->request('POST', $url, $requestOptions);
        } catch (\GuzzleHttp\Exception\ConnectException $error) {
            throw new HttpException(sprintf(Constants::COMMUNICATION_ERROR, 'Stripe', $error->getMessage()));
        }

        // Guzzle does not have a native way of catching timeout exceptions...
        // If we don't have a response at this point, it's likely due to a timeout.
        // @phpstan-ignore-next-line
        if (!isset($response)) {
            throw new TimeoutException(sprintf(Constants::NO_RESPONSE_ERROR, 'Stripe'));
        }

        $responseBody = $response->getBody();
        $httpStatus = $response->getStatusCode();
        $response = Requestor::interpretResponse($responseBody, $httpStatus);

        return $response;
    }

    /**
     * Submit the Stripe credit card token to EasyPost.
     *
     * @param string $referralApiKey
     * @param string $stripeObjectId
     * @param string $priority
     * @return mixed
     */
    private function createEasypostCreditCard(
        string $referralApiKey,
        string $stripeObjectId,
        string $priority = 'primary'
    ): mixed {
        $params = [
            'credit_card' => [
                'stripe_object_id' => $stripeObjectId,
                'priority' => $priority,
            ]
        ];

        $client = new EasyPostClient($referralApiKey);
        $response = Requestor::request($client, 'post', '/credit_cards', $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }
}

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
    public function all($params = null)
    {
        return self::allResources(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Retrieve the next page of Referral collection
     *
     * @param mixed $referrals
     * @param string $pageSize
     * @return mixed
     */
    public function getNextPage($referrals, $pageSize = null)
    {
        return $this->getNextPageResources(self::serviceModelClassName(self::class), $referrals, $pageSize);
    }

    /**
     * Create an referral.
     *
     * @param mixed $params
     * @return mixed
     */
    public function create($params = null)
    {
        if (!isset($params['user']) || !is_array($params['user'])) {
            $clone = $params;
            unset($params);
            $params['user'] = $clone;
        }

        return self::createResource(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Update an referral email.
     *
     * @param string $email
     * @param string $userId
     * @return void
     */
    public function updateEmail($email, $userId)
    {
        // TODO: Swap the order of these params so ID comes first to match all other functions
        // this will be a breaking change and must be done when the next major release happens
        $wrappedParams = [
            'user' => [
                'email' => $email
            ]
        ];

        Requestor::request($this->client, 'put', "/referral_customers/{$userId}", $wrappedParams);
    }

    /**
     * Add a credit card to a referral user.
     *
     * This function requires the Referral User's API key.
     *
     * @param string $referralApiKey
     * @param string $number
     * @param int $expirationMonth
     * @param int $expirationYear
     * @param string $cvc
     * @param string $primaryOrSecondary
     * @return mixed
     * @throws ExternalApiException
     */
    public function addCreditCard(
        $referralApiKey,
        $number,
        $expirationMonth,
        $expirationYear,
        $cvc,
        $primaryOrSecondary = 'primary'
    ) {
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

        $response = self::createEasypostCreditCard($referralApiKey, $stripeToken, $primaryOrSecondary);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Retrieves the public EasyPost Stripe API key.
     *
     * @return string
     */
    private function retrieveEasypostStripeApiKey()
    {
        $response = Requestor::request($this->client, 'get', '/partners/stripe_public_key');

        return $response['public_key'] ?? '';
    }

    /**
     * Retrieves the public EasyPost Stripe API key.
     *
     * @param string $number
     * @param int $expirationMonth
     * @param int @expirationYear
     * @param string $cvc
     * @param string $easypostStripeKey
     * @return mixed
     * @throws HttpException
     * @throws TimeoutException
     */
    private function createStripeToken($number, $expirationMonth, $expirationYear, $cvc, $easypostStripeKey)
    {
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

        $formEncodedParams = Requestor::urlEncode($creditCardDetails);
        $url = "https://api.stripe.com/v1/tokens?$formEncodedParams";

        $guzzleClient = new Client();

        $requestOptions['headers'] = $headers;
        $requestOptions['http_errors'] = false;

        try {
            $response = $guzzleClient->request('POST', $url, $requestOptions);
        } catch (\GuzzleHttp\Exception\ConnectException $error) {
            throw new HttpException(sprintf(Constants::COMMUNICATION_ERROR, 'Stripe', $error->getMessage()));
        }

        // Guzzle does not have a native way of catching timeout exceptions...
        // If we don't have a response at this point, it's likely due to a timeout.
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
     * @param string @priority
     * @return mixed
     */
    private function createEasypostCreditCard($referralApiKey, $stripeObjectId, $priority = 'primary')
    {
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

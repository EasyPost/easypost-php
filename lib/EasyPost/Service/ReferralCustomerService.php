<?php

namespace EasyPost\Service;

use EasyPost\Constant\Constants;
use EasyPost\EasyPostClient;
use EasyPost\Exception\Error;
use EasyPost\Http\Requestor;
use EasyPost\Util\InternalUtil;
use GuzzleHttp\Client;

/**
 * ReferralCustomer service containing all the logic to make API calls.
 */
class ReferralCustomerService extends BaseService
{
    private static $modelClass = 'ReferralCustomer';

    /**
     * Retrieve all referrals.
     *
     * @param mixed $params
     * @return mixed
     */
    public function all($params = null)
    {
        return self::allResources(self::$modelClass, $params);
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

        return self::createResource(self::$modelClass, $params);
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
     */
    public function addCreditCard($referralApiKey, $number, $expirationMonth, $expirationYear, $cvc, $primaryOrSecondary = 'primary')
    {
        $easypostStripeApiKey = self::retrieveEasypostStripeApiKey();

        try {
            $stripeToken = self::createStripeToken($number, $expirationMonth, $expirationYear, $cvc, $easypostStripeApiKey);
        } catch (\Exception $error) {
            throw new Error('Could not send card details to Stripe, please try again later');
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
            $message = 'Unexpected error communicating with Stripe. If this problem persists please let us know at ' . Constants::SUPPORT_EMAIL . ".{$error->getMessage()}";
            throw new Error($message, null, null);
        }

        // Guzzle does not have a native way of catching timeout exceptions... If we don't have a response at this point, it's likely due to a timeout
        if (!isset($response)) {
            throw new Error('Did not receive a response from the API.', null, null);
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

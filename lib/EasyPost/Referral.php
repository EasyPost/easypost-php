<?php

namespace EasyPost;

/**
 * @package EasyPost
 * @property string $id
 * @property string $object
 * @property string $parent_id
 * @property string $name
 * @property string $email
 * @property string $phone_number
 * @property string $balance
 * @property string $price_per_shipment
 * @property string $recharge_amount
 * @property string $secondary_recharge_amount
 * @property string $recharge_threshold
 * @property string $cc_fee_rate
 * @property string $insurance_fee_rate
 * @property string $insurance_fee_minimum
 * @property User[] $children
 */
class Referral extends EasypostResource
{
    /**
     * Retrieve all referrals.
     *
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     */
    public static function all($params = null, $apiKey = null)
    {
        return self::allResources('referralCustomer', $params, $apiKey);
    }

    /**
     * Create an referral.
     *
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        if (!isset($params['user']) || !is_array($params['user'])) {
            $clone = $params;
            unset($params);
            $params['user'] = $clone;
        }

        return self::createResource('referralCustomer', $params, $apiKey);
    }

    /**
     * Update an referral email.
     *
     * @param string $email
     * @param string $userId
     * @param string $apiKey
     * @return boolean
     */
    public static function update_email($email, $userId, $apiKey = null)
    {
        $wrappedParams = [
            'user' => [
                'email' => $email
            ]
        ];

        $requestor = new Requestor($apiKey);
        list($response, $apiKey) = $requestor->request('put', "/referral_customers/{$userId}", $wrappedParams);

        // Return true if successful, an error will be thrown if the request failed
        return true;
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
    public static function add_credit_card($referralApiKey, $number, $expirationMonth, $expirationYear, $cvc, $primaryOrSecondary = 'primary')
    {
        $easypostStripeApiKey = Referral::retrieve_easypost_stripe_api_key();

        try {
            $stripeToken = Referral::create_stripe_token($number, $expirationMonth, $expirationYear, $cvc, $easypostStripeApiKey);
        } catch (\Exception $error) {
            throw new Error('Could not send card details to Stripe, please try again later');
        }

        $stripeToken = $stripeToken['id'] ?? '';

        $response = Referral::create_easypost_credit_card($referralApiKey, $stripeToken, $primaryOrSecondary);

        return Util::convertToEasyPostObject($response, null);
    }

    /**
     * Retrieves the public EasyPost Stripe API key.
     *
     * @return string
     */
    private static function retrieve_easypost_stripe_api_key()
    {
        $requestor = new Requestor();
        list($response, $apiKey) = $requestor->request('get', '/partners/stripe_public_key');

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
    private static function create_stripe_token($number, $expirationMonth, $expirationYear, $cvc, $easypostStripeKey)
    {
        $headers = [
            'Content-Type: application/x-www-form-urlencoded',
            "Authorization: Bearer $easypostStripeKey",
        ];

        $creditCardDetails = [
            'card' => [
                'number' => $number,
                'exp_month' => $expirationMonth,
                'exp_year' => $expirationYear,
                'cvc' => $cvc,
            ]
        ];

        $requestor = new Requestor();
        $formEncodedParams = $requestor->urlEncode($creditCardDetails);
        $url = "https://api.stripe.com/v1/tokens?$formEncodedParams";

        list($httpBody, $httpStatus) = $requestor->curlRequest('POST', $url, $headers, null);
        $response = $requestor->interpretResponse($httpBody, $httpStatus);

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
    private static function create_easypost_credit_card($referralApiKey, $stripeObjectId, $priority = 'primary')
    {
        $params = [
            'credit_card' => [
                'stripe_object_id' => $stripeObjectId,
                'priority' => $priority,
            ]
        ];

        $requestor = new Requestor();
        list($response, $apiKey) = $requestor->request('post', '/credit_cards', $params);

        return $response;
    }
}

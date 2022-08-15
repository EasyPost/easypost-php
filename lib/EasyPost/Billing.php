<?php

namespace EasyPost;

/**
 * @package EasyPost
 * @property string $id
 * @property string $object
 * @property Object $primary_payment_method
 * @property Object $secondary_payment_method
 */
class Billing extends EasypostResource
{
    /**
     * Retrieve all payment methods.
     *
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     */
    public static function retrieve_payment_methods($params = null, $apiKey = null)
    {
        $paymentMethods = self::allResources('paymentMethod', $params, $apiKey);

        if ($paymentMethods->id == null) {
            throw new Error('Billing has not been setup for this user. Please add a payment method.');
        }

        return Util::convertToEasyPostObject($paymentMethods, $apiKey);
    }

    /**
     * Fund your EasyPost wallet by charging your primary or secondary payment method.
     *
     * @param string $amount
     * @param string $primary_or_secondary
     * @param string $apiKey
     * @return mixed
     */
    public static function fund_wallet($amount, $primary_or_secondary = 'primary', $apiKey = null)
    {
        [$paymentMethodEndpoint, $paymentMethodId] = Billing::get_payment_info(strtolower($primary_or_secondary));

        $url = $paymentMethodEndpoint . "/$paymentMethodId/charges";
        $wrappedParams = ['amount' => $amount];
        $requestor = new Requestor($apiKey);
        list($response, $apiKey) = $requestor->request('post', $url, $wrappedParams);

        // Return true if succeeds, an error will be thrown if it fails
        return true;
    }

    /**
     * Delete a payment method.
     *
     * @param string $primary_or_secondary
     * @param string $apiKey
     * @return mixed
     */
    public static function delete_payment_method($primary_or_secondary, $apiKey = null)
    {
        [$paymentMethodEndpoint, $paymentMethodId] = Billing::get_payment_info(strtolower($primary_or_secondary));

        $url = $paymentMethodEndpoint . "/$paymentMethodId";
        $requestor = new Requestor($apiKey);
        list($response, $apiKey) = $requestor->request('delete', $url);

        // Return true if succeeds, an error will be thrown if it fails
        return true;
    }

    /**
     * Get payment info (type of the payment method and ID of the payment method)
     *
     * @param string $primary_or_secondary
     * @return array
     */
    private static function get_payment_info($primary_or_secondary = 'primary')
    {
        $paymentMethods = Billing::retrieve_payment_methods();
        $paymentMethodMap = [
            'primary' => 'primary_payment_method',
            'secondary' => 'secondary_payment_method'
        ];
        $paymentMethodToUse = $paymentMethodMap[$primary_or_secondary] ?? null;

        $errorString = 'The chosen payment method is not valid. Please try again.';

        if ($paymentMethodToUse != null && $paymentMethods->$paymentMethodToUse->id != null) {
            $paymentMethodId = $paymentMethods->$paymentMethodToUse->id;
            if (strpos($paymentMethodId, 'card_') === 0) {
                $endpoint = '/credit_cards';
            } else if (strpos($paymentMethodId, 'bank_') === 0) {
                $endpoint = '/bank_accounts';
            } else {
                throw new Error($errorString);
            }
        } else {
            throw new Error($errorString);
        }

        return [$endpoint, $paymentMethodId];
    }
}

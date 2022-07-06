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
        $payment_methods = self::_all("paymentMethod", $params, $apiKey);

        if ($payment_methods->id == null) {
            throw new Error('Billing has not been setup for this user. Please add a payment method.');
        }

        return Util::convertToEasyPostObject($payment_methods, $apiKey);
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
        [$payment_method_endpoint, $payment_method_id] = Billing::get_payment_info(strtolower($primary_or_secondary));

        $url = $payment_method_endpoint . "/$payment_method_id/charges";
        $wrapped_params = ['amount' => $amount];
        $requestor = new Requestor($apiKey);
        list($response, $apiKey) = $requestor->request('post', $url, $wrapped_params);

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
        [$payment_method_endpoint, $payment_method_id] = Billing::get_payment_info(strtolower($primary_or_secondary));

        $url = $payment_method_endpoint . "/$payment_method_id";
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
        $payment_methods = Billing::retrieve_payment_methods();
        $payment_method_map = [
            'primary' => 'primary_payment_method',
            'secondary' => 'secondary_payment_method'
        ];
        $payment_method_to_use = $payment_method_map[$primary_or_secondary] ?? null;

        $error_string = 'The chosen payment method is not valid. Please try again.';

        if ($payment_method_to_use != null && $payment_methods->$payment_method_to_use->id != null) {
            $payment_method_id = $payment_methods->$payment_method_to_use->id;
            if (strpos($payment_method_id, 'card_') === 0) {
                $endpoint = '/credit_cards';
            } else if (strpos($payment_method_id, 'bank_') === 0) {
                $endpoint = '/bank_accounts';
            } else {
                throw new Error($error_string);
            }
        } else {
            throw new Error($error_string);
        }

        return [$endpoint, $payment_method_id];
    }
}

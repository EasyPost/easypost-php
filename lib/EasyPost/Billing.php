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
        return $payment_methods;
    }

    /**
     * Fund your EasyPost wallet by charging your primary or secondary payment method.
     *
     * @param string $amount
     * @param string $primary_or_secondary
     * @param string $api_key
     * @return mixed
     */
    public static function fund_wallet($amount, $primary_or_secondary = 'primary', $api_key = null)
    {
        $payment_method_info = Billing::get_payment_info($primary_or_secondary);
        $payment_method_endpoint = $payment_method_info[0];
        $payment_method_id = $payment_method_info[1];

        $url = $payment_method_endpoint . "/$payment_method_id/charges";
        $wrapped_params = ['amount' => $amount];
        $requestor = new Requestor($api_key);
        list($response, $api_key) = $requestor->request('post', $url, $wrapped_params);
        return Util::convertToEasyPostObject($response, $api_key);
    }

    /**
     * Delete a payment method.
     *
     * @param string $primary_or_secondary
     * @param string $api_key
     * @return mixed
     */
    public static function delete_payment_method($primary_or_secondary, $api_key = null)
    {
        $payment_method_info = Billing::get_payment_info($primary_or_secondary);
        $payment_method_endpoint = $payment_method_info[0];
        $payment_method_id = $payment_method_info[1];

        $url = $payment_method_endpoint . "/$payment_method_id";
        $requestor = new Requestor($api_key);
        list($response, $api_key) = $requestor->request('delete', $url);
        return Util::convertToEasyPostObject($response, $api_key);
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

        if ($payment_methods->$payment_method_to_use != null) {
            $payment_method_id = $payment_methods->$payment_method_to_use->id;
        }

        if ($payment_method_to_use !== null && $payment_method_id !== null) {
            if (strpos($payment_method_id, 'card_') !== 0) {
                return array('credit_cards', $payment_methods->$payment_method_to_use->id);
            } else if (strpos($payment_method_id, 'bank_') !== 0) {
                return array('bank_accounts', $payment_methods->$payment_method_to_use->id);
            }
        }

        throw new Error('The chosen payment method is not valid. Please try again.');
    }
}

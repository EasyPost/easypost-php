<?php

namespace EasyPost;

/**
 * @package EasyPost
 * @property string $id
 * @property string $object
 * @property string $name
 * @property string $last4
 * @property string $expiration_month
 * @property string $expiration_year
 * @property string $brand
 */
class CreditCard extends EasypostResource
{
    /**
     * Fund a credit card.
     *
     * @param string $amount
     * @param string $primary_or_secondary
     * @param string $api_key
     * @return mixed
     */
    public static function fund($amount, $primary_or_secondary = 'primary', $api_key = null)
    {
        $payment_methods = PaymentMethod::all();
        $payment_method_map = [
            'primary' => 'primary_payment_method',
            'secondary' => 'secondary_payment_method'
        ];
        $payment_method_to_use = $payment_method_map[$primary_or_secondary] ?? null;

        if ($payment_methods->$payment_method_to_use != null) {
            $card_id = $payment_methods->$payment_method_to_use->id;
        }

        if ($payment_method_to_use === null || $card_id === null || strpos($card_id, 'card_') !== 0) {
            throw new Error('The chosen payment method is not a credit card. Please try again.');
        }

        $url = self::classUrl(get_class()) . "/$card_id/charges";
        $wrapped_params = ['amount' => $amount];
        $requestor = new Requestor($api_key);
        list($response, $api_key) = $requestor->request('post', $url, $wrapped_params);
        return Util::convertToEasyPostObject($response, $api_key);
    }

    /**
     * Delete a credit card.
     *
     * @param string $credit_card_id
     * @param string $api_key
     * @return mixed
     */
    public static function delete($credit_card_id, $api_key = null)
    {
        $url = self::classUrl(get_class()) . "/$credit_card_id";
        $requestor = new Requestor($api_key);
        list($response, $api_key) = $requestor->request('delete', $url);
        return Util::convertToEasyPostObject($response, $api_key);
    }
}

<?php

namespace EasyPost;

class BankAccount extends EasypostResource
{
    /**
     * Fund your EasyPost wallet by charging your bank account.
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
            $bank_id = $payment_methods->$payment_method_to_use->id;
        }

        if ($payment_method_to_use === null || $bank_id === null || strpos($bank_id, 'bank_') !== 0) {
            throw new Error('The chosen payment method is not a bank account. Please try again.');
        }

        $url = self::classUrl(get_class()) . "/$bank_id/charges";
        $wrapped_params = ['amount' => $amount];
        $requestor = new Requestor($api_key);
        list($response, $api_key) = $requestor->request('post', $url, $wrapped_params);
        return Util::convertToEasyPostObject($response, $api_key);
    }

    /**
     * Delete a bank account by ID.
     *
     * @param string $bank_account_id
     * @param string $api_key
     * @return mixed
     */
    public static function delete($bank_account_id, $api_key = null)
    {
        $url = self::classUrl(get_class()) . "/$bank_account_id";
        $requestor = new Requestor($api_key);
        list($response, $api_key) = $requestor->request('delete', $url);
        return Util::convertToEasyPostObject($response, $api_key);
    }
}

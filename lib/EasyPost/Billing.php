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
    public static function retrievePaymentMethods($params = null, $apiKey = null)
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
     * @param string $primaryOrSecondary
     * @param string $apiKey
     * @return void
     */
    public static function fundWallet($amount, $primaryOrSecondary = 'primary', $apiKey = null)
    {
        [$paymentMethodEndpoint, $paymentMethodId] = Billing::getPaymentInfo(strtolower($primaryOrSecondary));

        $url = $paymentMethodEndpoint . "/$paymentMethodId/charges";
        $wrappedParams = ['amount' => $amount];
        $requestor = new Requestor($apiKey);
        $requestor->request('post', $url, $wrappedParams);
    }

    /**
     * Delete a payment method.
     *
     * @param string $primaryOrSecondary
     * @param string $apiKey
     * @return void
     */
    public static function deletePaymentMethod($primaryOrSecondary, $apiKey = null)
    {
        [$paymentMethodEndpoint, $paymentMethodId] = Billing::getPaymentInfo(strtolower($primaryOrSecondary));

        $url = $paymentMethodEndpoint . "/$paymentMethodId";
        $requestor = new Requestor($apiKey);
        $requestor->request('delete', $url);
    }

    /**
     * Get payment info (type of the payment method and ID of the payment method)
     *
     * @param string $primaryOrSecondary
     * @return array
     */
    private static function getPaymentInfo($primaryOrSecondary = 'primary')
    {
        $paymentMethods = Billing::retrievePaymentMethods();
        $paymentMethodMap = [
            'primary' => 'primary_payment_method',
            'secondary' => 'secondary_payment_method'
        ];
        $paymentMethodToUse = $paymentMethodMap[$primaryOrSecondary] ?? null;

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

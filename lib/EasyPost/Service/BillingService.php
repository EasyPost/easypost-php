<?php

namespace EasyPost\Service;

use EasyPost\Exception\Error;
use EasyPost\Http\Requestor;
use EasyPost\Util\Util;

/**
 * Billing service containing all the logic to make API calls.
 */
class BillingService extends BaseService
{
    private static $modelClass = 'PaymentMethod';

    /**
     * Retrieve all payment methods.
     *
     * @param mixed $params
     * @return mixed
     */
    public function retrievePaymentMethods($params = null)
    {
        $paymentMethods = self::allResources(self::$modelClass, $params);

        if ($paymentMethods->id == null) {
            throw new Error('Billing has not been setup for this user. Please add a payment method.');
        }

        return Util::convertToEasyPostObject($this->client, $paymentMethods);
    }

    /**
     * Fund your EasyPost wallet by charging your primary or secondary payment method.
     *
     * @param string $amount
     * @param string $primaryOrSecondary
     * @return void
     */
    public function fundWallet($amount, $primaryOrSecondary = 'primary')
    {
        [$paymentMethodEndpoint, $paymentMethodId] = self::getPaymentInfo(strtolower($primaryOrSecondary));

        $url = $paymentMethodEndpoint . "/$paymentMethodId/charges";
        $wrappedParams = ['amount' => $amount];
        $requestor = new Requestor($this->client);
        $requestor->request('post', $url, $wrappedParams);
    }

    /**
     * Delete a payment method.
     *
     * @param string $primaryOrSecondary
     * @return void
     */
    public function deletePaymentMethod($primaryOrSecondary)
    {
        [$paymentMethodEndpoint, $paymentMethodId] = self::getPaymentInfo(strtolower($primaryOrSecondary));

        $url = $paymentMethodEndpoint . "/$paymentMethodId";
        $requestor = new Requestor($this->client);
        $requestor->request('delete', $url);
    }

    /**
     * Get payment info (type of the payment method and ID of the payment method)
     *
     * @param string $primaryOrSecondary
     * @return array
     */
    private function getPaymentInfo($primaryOrSecondary = 'primary')
    {
        $paymentMethods = self::retrievePaymentMethods();
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

<?php

namespace EasyPost\Service;

use EasyPost\Constant\Constants;
use EasyPost\Exception\Api\PaymentException;
use EasyPost\Http\Requestor;
use EasyPost\Util\InternalUtil;

/**
 * Billing service containing all the logic to make API calls.
 */
class BillingService extends BaseService
{
    // Overridden here because the logic we need is tied to PaymentMethod
    private static string $modelClass = 'PaymentMethod';

    /**
     * Retrieve all payment methods.
     *
     * @param mixed $params
     * @return mixed
     * @throws PaymentException
     */
    public function retrievePaymentMethods(mixed $params = null): mixed
    {
        $paymentMethods = self::allResources(self::$modelClass, $params);

        if ($paymentMethods->id == null) {
            throw new PaymentException(Constants::NO_BILLING_ERROR);
        }

        return InternalUtil::convertToEasyPostObject($this->client, $paymentMethods);
    }

    /**
     * Fund your EasyPost wallet by charging your primary or secondary payment method.
     *
     * @param string $amount
     * @param string $priority
     * @return void
     */
    public function fundWallet(string $amount, string $priority = 'primary'): void
    {
        [$paymentMethodEndpoint, $paymentMethodId] = self::getPaymentInfo(strtolower($priority));

        $url = $paymentMethodEndpoint . "/$paymentMethodId/charges";
        $wrappedParams = ['amount' => $amount];

        Requestor::request($this->client, 'post', $url, $wrappedParams);
    }

    /**
     * Delete a payment method.
     *
     * @param string $priority
     * @return void
     */
    public function deletePaymentMethod(string $priority): void
    {
        [$paymentMethodEndpoint, $paymentMethodId] = self::getPaymentInfo(strtolower($priority));
        $url = $paymentMethodEndpoint . "/$paymentMethodId";

        Requestor::request($this->client, 'delete', $url);
    }

    /**
     * Get payment info (type of the payment method and ID of the payment method)
     *
     * @param string $priority
     * @return array<mixed>
     * @throws PaymentException
     */
    private function getPaymentInfo(string $priority = 'primary'): array
    {
        $paymentMethods = self::retrievePaymentMethods();
        $paymentMethodMap = [
            'primary' => 'primary_payment_method',
            'secondary' => 'secondary_payment_method'
        ];
        $paymentMethodToUse = $paymentMethodMap[$priority] ?? null;

        if ($paymentMethodToUse != null && $paymentMethods->$paymentMethodToUse->id != null) {
            $paymentMethodId = $paymentMethods->$paymentMethodToUse->id;
            $paymentMethodObjectType = $paymentMethods->$paymentMethodToUse->object;
            if ($paymentMethodObjectType == 'CreditCard') {
                $endpoint = '/credit_cards';
            } else if ($paymentMethodObjectType == 'BankAccount') {
                $endpoint = '/bank_accounts';
            } else {
                throw new PaymentException(Constants::INVALID_PAYMENT_METHOD_ERROR);
            }
        } else {
            throw new PaymentException(Constants::INVALID_PAYMENT_METHOD_ERROR);
        }

        return [$endpoint, $paymentMethodId];
    }
}

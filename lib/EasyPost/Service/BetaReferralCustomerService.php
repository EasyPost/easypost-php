<?php

namespace EasyPost\Service;

use EasyPost\Http\Requestor;
use EasyPost\Util\InternalUtil;

/**
 * ReferralCustomer service containing all the logic to make API calls.
 */
class BetaReferralCustomerService extends BaseService
{
    /**
     * Add a Stripe payment method to your EasyPost account.
     *
     * This endpoint uses a user's personal Stripe account. The `stripe_customer_id`
     * and `payment_method_reference` IDs both come from Stripe. By adding these to
     * EasyPost, we will associate your Stripe payment method with either your primary
     * or secondary EasyPost payment method.
     *
     * @param string $stripeCustomerId
     * @param string $paymentMethodReference
     * @param string $primaryOrSecondary
     * @return mixed
     */
    public function addPaymentMethod($stripeCustomerId, $paymentMethodReference, $primaryOrSecondary = 'primary')
    {
        $params = [
            'payment_method' => [
                'stripe_customer_id' => $stripeCustomerId,
                'payment_method_reference' => $paymentMethodReference,
                'priority' => $primaryOrSecondary
            ]
        ];

        $response = Requestor::request($this->client, 'post', '/referral_customers/payment_method', $params, true);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Refund a ReferralCustomer wallet by specifying an amount.
     *
     * @param int $refundAmount
     * @return mixed
     */
    public function refundByAmount($refundAmount)
    {
        $params = ['refund_amount' => $refundAmount];

        $response = Requestor::request($this->client, 'post', '/referral_customers/refunds', $params, true);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Refund a ReferralCustomer wallet by specifying a payment log ID to completely refund.
     *
     * @param string $paymentLogId
     * @return mixed
     */
    public function refundByPaymentLog($paymentLogId)
    {
        $params = ['payment_log_id' => $paymentLogId];

        $response = Requestor::request($this->client, 'post', '/referral_customers/refunds', $params, true);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }
}

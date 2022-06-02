<?php

namespace EasyPost;

/**
 * @package EasyPost
 * @property string $id
 * @property string $object
 * @property CreditCard primary_payment_method
 * @property CreditCard secondary_payment_method
 */
class PaymentMethod extends EasypostResource
{
    /**
     * Retrieve all payment methods.
     *
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     */
    public static function all($params = null, $apiKey = null)
    {
        $response = self::_all(get_class(), $params, $apiKey);

        if ($response->id == null) {
            throw new Error('Billing has not been setup for this user. Please add a payment method.');
        }
        return $response;
    }
}

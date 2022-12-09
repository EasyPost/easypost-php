<?php

namespace EasyPost\Util;

use EasyPost\EasyPostObject;
use EasyPost\Exception\Error;
use Normalizer;

abstract class Util
{
    /**
     * Convert EasyPost object to an array.
     *
     * @param mixed $values
     * @return array
     */
    public static function convertEasyPostObjectToArray($values)
    {
        $results = [];
        foreach ($values as $k => $value) {
            if ($value instanceof EasyPostObject) {
                $results[$k] = $value->__toArray(true);
            } elseif (is_array($value)) {
                $results[$k] = self::convertEasyPostObjectToArray($value);
            } else {
                $results[$k] = $value;
            }
        }

        return $results;
    }

    /**
     * Get the lowest SmartRate from a list of SmartRates.
     *
     * To exclude a carrier or service, prepend the string with `!`.
     *
     * @param array $smartRates
     * @param int $deliveryDays
     * @param string $deliveryAccuracy
     * @return Rate
     */
    public static function getLowestSmartRate($smartRates, $deliveryDays, $deliveryAccuracy)
    {
        $validDeliveryAccuracyValues = [
            'percentile_50',
            'percentile_75',
            'percentile_85',
            'percentile_90',
            'percentile_95',
            'percentile_97',
            'percentile_99',
        ];
        $lowestSmartRate = false;

        if (!in_array(strtolower($deliveryAccuracy), $validDeliveryAccuracyValues)) {
            $jsonValidList = json_encode($validDeliveryAccuracyValues);
            throw new Error("Invalid delivery_accuracy value, must be one of: $jsonValidList");
        }

        foreach ($smartRates as $rate) {
            if ($rate['time_in_transit'][$deliveryAccuracy] > intval($deliveryDays)) {
                continue;
            } elseif (!$lowestSmartRate || floatval($rate['rate']) < floatval($lowestSmartRate['rate'])) {
                $lowestSmartRate = $rate;
            }
        }

        if ($lowestSmartRate == false) {
            throw new Error('No rates found.');
        }

        return $lowestSmartRate;
    }

    /**
     * Validate a webhook originated from EasyPost by comparing the HMAC header to a shared secret.
     * If the signatures do not match, an error will be raised signifying the webhook either did not originate
     * from EasyPost or the secrets do not match. If the signatures do match, the `event_body` will be returned as JSON.
     *
     * @param mixed $eventBody
     * @param mixed $headers
     * @param string $webhookSecret
     * @return mixed
     */
    public static function validateWebhook($eventBody, $headers, $webhookSecret)
    {
        $easypostHmacSignature = $headers['X-Hmac-Signature'] ?? null;

        if ($easypostHmacSignature != null) {
            $normalizedSecret = Normalizer::normalize($webhookSecret, Normalizer::FORM_KD);
            $encodedSecret = mb_convert_encoding($normalizedSecret, 'UTF-8');

            $expectedSignature = hash_hmac('sha256', $eventBody, $encodedSecret);
            $digest = "hmac-sha256-hex=$expectedSignature";

            if (hash_equals($digest, $easypostHmacSignature)) {
                $webhookBody = json_decode($eventBody);
            } else {
                throw new Error('Webhook received did not originate from EasyPost or had a webhook secret mismatch.');
            }
        } else {
            throw new Error('Webhook received does not contain an HMAC signature.');
        }

        return $webhookBody;
    }

    /**
     * Receive an event (convert JSON string to object).
     *
     * @param string $rawInput
     * @return mixed
     * @throws Error
     */
    public static function receiveEvent($rawInput = null)
    {
        if ($rawInput == null) {
            throw new Error('The raw input must be set');
        }
        $values = json_decode($rawInput, true);
        if ($values != null) {
            return EasyPostObject::constructFrom(null, $values, '\EasyPost\\' . 'Event');
        } else {
            throw new Error('There was a problem decoding the webhook');
        }
    }
}

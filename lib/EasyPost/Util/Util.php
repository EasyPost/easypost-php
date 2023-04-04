<?php

namespace EasyPost\Util;

use EasyPost\Constant\Constants;
use EasyPost\EasyPostObject;
use EasyPost\Exception\Api\EncodingException;
use EasyPost\Exception\General\FilteringException;
use EasyPost\Exception\General\InvalidParameterException;
use EasyPost\Exception\General\MissingParameterException;
use EasyPost\Exception\General\SignatureVerificationException;
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
     * @throws EasyPostException
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
            throw new InvalidParameterException(
                sprintf(Constants::INVALID_PARAMETER_ERROR_WITH_SUGGESTION, 'delivery_accuracy', $jsonValidList)
            );
        }

        foreach ($smartRates as $rate) {
            if ($rate['time_in_transit'][$deliveryAccuracy] > intval($deliveryDays)) {
                continue;
            } elseif (!$lowestSmartRate || floatval($rate['rate']) < floatval($lowestSmartRate['rate'])) {
                $lowestSmartRate = $rate;
            }
        }

        if ($lowestSmartRate == false) {
            throw new FilteringException(Constants::NO_RATES_ERROR);
        }

        return $lowestSmartRate;
    }

    /**
     * Validate a webhook originated from EasyPost by comparing the HMAC header to a shared secret.
     * If the signatures do not match, an error will be raised signifying the webhook either did not originate
     * from EasyPost or the secrets do not match. If the signatures do match, the `event_body` will be returned as JSON
     *
     * @param mixed $eventBody
     * @param mixed $headers
     * @param string $webhookSecret
     * @return mixed
     * @throws EasyPostException
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
                throw new SignatureVerificationException(Constants::INVALID_WEBHOOK_VALIDATION_ERROR);
            }
        } else {
            throw new SignatureVerificationException(Constants::INVALID_SIGNATURE_ERROR);
        }

        return $webhookBody;
    }

    /**
     * Receive an event (convert JSON string to object).
     *
     * @param string $rawInput
     * @return mixed
     * @throws EasyPostException
     */
    public static function receiveEvent($rawInput = null)
    {
        if ($rawInput == null) {
            throw new MissingParameterException(sprintf(Constants::MISSING_PARAMETER_ERROR, 'rawInput'));
        }
        $values = json_decode($rawInput, true);
        if ($values != null) {
            return EasyPostObject::constructFrom(null, $values, '\EasyPost\\' . 'Event');
        } else {
            throw new EncodingException(Constants::DECODE_WEBHOOK_ERROR);
        }
    }

    /**
     * Get the lowest stateless rate.
     * To exclude a carrier or service, prepend the string with `!`.
     *
     * @param array statelessRates
     * @param array $carriers
     * @param array $services
     * @return Rate
     * @throws \EasyPost\Exception\EasyPostException
     */
    public static function getLowestStatelessRate($statelessRates, $carriers = [], $services = [])
    {
        $lowestStatelessRate = false;
        $carriersInclude = [];
        $carriersExclude = [];
        $servicesInclude = [];
        $servicesExclude = [];

        if (!is_array($carriers)) {
            $carriers = explode(',', $carriers);
        }
        for ($i = 0; $i < count($carriers); $i++) {
            $carriers[$i] = trim(strtolower($carriers[$i]));
            if (substr($carriers[$i], 0, 1) == '!') {
                $carriersExclude[] = substr($carriers[$i], 1);
            } else {
                $carriersInclude[] = $carriers[$i];
            }
        }

        if (!is_array($services)) {
            $services = explode(',', $services);
        }
        for ($i = 0; $i < count($services); $i++) {
            $services[$i] = trim(strtolower($services[$i]));
            if (substr($services[$i], 0, 1) == '!') {
                $servicesExclude[] = substr($services[$i], 1);
            } else {
                $servicesInclude[] = $services[$i];
            }
        }

        for ($i = 0; $i < count($statelessRates); $i++) {
            $rateCarrier = strtolower($statelessRates[$i]->carrier);
            if (!empty($carriersInclude[0]) && !in_array($rateCarrier, $carriersInclude)) {
                continue;
            }
            if (!empty($carriersExclude[0]) && in_array($rateCarrier, $carriersExclude)) {
                continue;
            }

            $rateService = strtolower($statelessRates[$i]->service);
            if (!empty($servicesInclude[0]) && !in_array($rateService, $servicesInclude)) {
                continue;
            }
            if (!empty($servicesExclude[0]) && in_array($rateService, $servicesExclude)) {
                continue;
            }

            if (!$lowestStatelessRate || floatval($statelessRates[$i]->rate) < floatval($lowestStatelessRate->rate)) {
                $lowestStatelessRate = clone ($statelessRates[$i]);
            }
        }

        if ($lowestStatelessRate == false) {
            throw new FilteringException(Constants::NO_RATES_ERROR);
        }

        return $lowestStatelessRate;
    }
}

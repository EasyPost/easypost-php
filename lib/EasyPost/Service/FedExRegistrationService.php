<?php

namespace EasyPost\Service;

use EasyPost\Http\Requestor;
use EasyPost\Util\InternalUtil;

/**
 * FedExRegistration service containing all the logic to make API calls.
 */
class FedExRegistrationService extends BaseService
{
    /**
     * Register the billing address for a FedEx account.
     * Advanced method for custom parameter structures.
     *
     * @param string $fedexAccountNumber
     * @param mixed $params
     * @return mixed
     */
    public function registerAddress(string $fedexAccountNumber, mixed $params = null): mixed
    {
        $wrappedParams = $this->wrapAddressValidation($params);
        $url = "/fedex_registrations/{$fedexAccountNumber}/address";

        $response = Requestor::request($this->client, 'post', $url, $wrappedParams);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Request a PIN for FedEx account verification.
     *
     * @param string $fedexAccountNumber
     * @param string $pinMethodOption
     * @return mixed
     */
    public function requestPin(string $fedexAccountNumber, string $pinMethodOption): mixed
    {
        $wrappedParams = [
            'pin_method' => [
                'option' => $pinMethodOption,
            ],
        ];
        $url = "/fedex_registrations/{$fedexAccountNumber}/pin";

        $response = Requestor::request($this->client, 'post', $url, $wrappedParams);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Validate the PIN entered by the user for FedEx account verification.
     *
     * @param string $fedexAccountNumber
     * @param mixed $params
     * @return mixed
     */
    public function validatePin(string $fedexAccountNumber, mixed $params = null): mixed
    {
        $wrappedParams = $this->wrapPinValidation($params);
        $url = "/fedex_registrations/{$fedexAccountNumber}/pin/validate";

        $response = Requestor::request($this->client, 'post', $url, $wrappedParams);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Submit invoice information to complete FedEx account registration.
     *
     * @param string $fedexAccountNumber
     * @param mixed $params
     * @return mixed
     */
    public function submitInvoice(string $fedexAccountNumber, mixed $params = null): mixed
    {
        $wrappedParams = $this->wrapInvoiceValidation($params);
        $url = "/fedex_registrations/{$fedexAccountNumber}/invoice";

        $response = Requestor::request($this->client, 'post', $url, $wrappedParams);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Wraps address validation parameters and ensures the "name" field exists.
     * If not present, generates a UUID (with hyphens removed) as the name.
     *
     * @param mixed $params
     * @return array<string, mixed>
     */
    private function wrapAddressValidation(mixed $params): array
    {
        $wrappedParams = [];

        if (isset($params['address_validation'])) {
            $addressValidation = $params['address_validation'];
            $this->ensureNameField($addressValidation);
            $wrappedParams['address_validation'] = $addressValidation;
        }

        if (isset($params['easypost_details'])) {
            $wrappedParams['easypost_details'] = $params['easypost_details'];
        }

        return $wrappedParams;
    }

    /**
     * Wraps PIN validation parameters and ensures the "name" field exists.
     * If not present, generates a UUID (with hyphens removed) as the name.
     *
     * @param mixed $params
     * @return array<string, mixed>
     */
    private function wrapPinValidation(mixed $params): array
    {
        $wrappedParams = [];

        if (isset($params['pin_validation'])) {
            $pinValidation = $params['pin_validation'];
            $this->ensureNameField($pinValidation);
            $wrappedParams['pin_validation'] = $pinValidation;
        }

        if (isset($params['easypost_details'])) {
            $wrappedParams['easypost_details'] = $params['easypost_details'];
        }

        return $wrappedParams;
    }

    /**
     * Wraps invoice validation parameters and ensures the "name" field exists.
     * If not present, generates a UUID (with hyphens removed) as the name.
     *
     * @param mixed $params
     * @return array<string, mixed>
     */
    private function wrapInvoiceValidation(mixed $params): array
    {
        $wrappedParams = [];

        if (isset($params['invoice_validation'])) {
            $invoiceValidation = $params['invoice_validation'];
            $this->ensureNameField($invoiceValidation);
            $wrappedParams['invoice_validation'] = $invoiceValidation;
        }

        if (isset($params['easypost_details'])) {
            $wrappedParams['easypost_details'] = $params['easypost_details'];
        }

        return $wrappedParams;
    }

    /**
     * Ensures the "name" field exists in the provided array.
     * If not present, generates a unique ID as the name.
     * This follows the pattern used in the web UI implementation.
     *
     * @param array<string, mixed> &$array
     * @return void
     */
    private function ensureNameField(array &$array): void
    {
        if (!isset($array['name'])) {
            $array['name'] = uniqid();
        }
    }
}

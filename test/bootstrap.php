<?php

use allejo\VCR\VCRCleaner;
use VCR\VCR;

if (!file_exists('test/cassettes')) {
    mkdir('test/cassettes', 0755, true);
}

VCR::configure()->setCassettePath('test/cassettes')
    ->setStorage('yaml')
    ->setMode('once');

define('RESPONSE_BODY_SCRUBBERS', [
    ['api_keys', []],
    ['children', []],
    ['client_ip', '<REDACTED>'],
    ['credentials', []],
    ['email', '<REDACTED>'],
    ['key', '<REDACTED>'],
    ['keys', []],
    ['phone_number', '<REDACTED>'],
    ['phone', '<REDACTED>'],
    ['test_credentials', []],
]);

VCRCleaner::enable([
    'request' => [
        'ignoreHeaders' => [
            'Authorization',
            'User-Agent',
        ],
        'ignoreQueryFields' => [
            'card',
        ]
    ],
    'response' => [
        # Scrub sensitive data from response bodies (at the root level or in a root list)
        # prior to recording the cassette.
        # This function DOES NOT scrub data in nested objects or lists.
        'bodyScrubbers' => [
            function ($responseBody) {
                if (isset($responseBody)) {
                    $responseBodyJson = json_decode($responseBody, true);

                    foreach (RESPONSE_BODY_SCRUBBERS as $scrubber) {
                        $key = $scrubber[0];
                        $replacement = $scrubber[1];
                        # PHP treats JSON objects (associative arrays) and lists (sequential arrays) as the same thing (array)
                        # so we check what kind of array it is here and scrub the data accordingly
                        if (array_keys($responseBodyJson) == range(0, count($responseBodyJson) - 1)) {
                            foreach ($responseBodyJson as $index => $element) {
                                if (is_array($element)) {
                                    if (array_key_exists($key, $element)) {
                                        $responseBodyJson[$index][$key] = $replacement;
                                    }
                                }
                            }
                        } else {
                            if (array_key_exists($key, $responseBodyJson)) {
                                $responseBodyJson[$key] = $replacement;
                            }
                        }
                    }

                    $responseBody = json_encode($responseBodyJson);
                }
                return $responseBody;
            }
        ],
    ],
]);

VCR::turnOn();

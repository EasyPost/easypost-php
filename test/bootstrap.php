<?php

use allejo\VCR\VCRCleaner;
use VCR\VCR;

if (!file_exists('test/cassettes')) {
    mkdir('test/cassettes', 0755, true);
}

VCR::configure()->setCassettePath('test/cassettes')
    ->setStorage('yaml')
    ->setMode('once');

define('CASSETTE_REPLACEMENT_VALUE', '<REDACTED>');
define('RESPONSE_BODY_SCRUBBERS', [
    'api_keys',
    'children',
    'client_ip',
    'credentials',
    'email',
    'key',
    'keys',
    'phone_number',
    'phone',
    'test_credentials',
]);

VCRCleaner::enable([
    'request' => [
        'ignoreHeaders' => [
            'Authorization',
            'User-Agent',
        ],
        # TODO: Validate this works when we introduce the Beta Referral class
        'postFieldScrubbers' => [
            function ($postFields) {
                if (isset($postFields['card[number]'])) {
                    $postFields['card[number]'] = CASSETTE_REPLACEMENT_VALUE;
                }
                if (isset($postFields['card[number]'])) {
                    $postFields['card[cvc]'] = CASSETTE_REPLACEMENT_VALUE;
                }

                return $postFields;
            }
        ],
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
                        # PHP treats JSON objects (associative arrays) and lists (sequential arrays) as the same thing (array)
                        # so we check what kind of array it is here and scrub the data accordingly
                        if (array_keys($responseBodyJson) == range(0, count($responseBodyJson) - 1)) {
                            foreach ($responseBodyJson as $index => $element) {
                                if (array_key_exists($scrubber, $element)) {
                                    $responseBodyJson[$index][$scrubber] = CASSETTE_REPLACEMENT_VALUE;
                                }
                            }
                        } else {
                            if (array_key_exists($scrubber, $responseBodyJson)) {
                                $responseBodyJson[$scrubber] = CASSETTE_REPLACEMENT_VALUE;
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

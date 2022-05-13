<?php

use VCR\VCR;
use allejo\VCR\VCRCleaner;

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
            'X-Client-User-Agent',
        ],
        # TODO: Validate this works when we introduce the Beta Referral class
        'postFieldScrubbers' => [
            function ($post_fields) {
                if (isset($post_fields['card[number]'])) {
                    $post_fields['card[number]'] = CASSETTE_REPLACEMENT_VALUE;
                }
                if (isset($post_fields['card[number]'])) {
                    $post_fields['card[cvc]'] = CASSETTE_REPLACEMENT_VALUE;
                }

                return $post_fields;
            }
        ],
    ],
    'response' => [
        # Scrub sensitive data from response bodies (at the root level or in a root list)
        # prior to recording the cassette.
        # This function DOES NOT scrub data in nested objects or lists.
        'bodyScrubbers' => [
            function ($response_body) {
                if (isset($response_body)) {
                    $response_body_json = json_decode($response_body, true);

                    foreach (RESPONSE_BODY_SCRUBBERS as $scrubber) {
                        # PHP treats JSON objects (associative arrays) and lists (sequential arrays) as the same thing (array)
                        # so we check what kind of array it is here and scrub the data accordingly
                        if (array_keys($response_body_json) == range(0, count($response_body_json) - 1)) {
                            foreach ($response_body_json as $index => $element) {
                                if (array_key_exists($scrubber, $element)) {
                                    $response_body_json[$index][$scrubber] = CASSETTE_REPLACEMENT_VALUE;
                                }
                            }
                        } else {
                            if (array_key_exists($scrubber, $response_body_json)) {
                                $response_body_json[$scrubber] = CASSETTE_REPLACEMENT_VALUE;
                            }
                        }
                    }

                    $response_body = json_encode($response_body_json);
                }
                return $response_body;
            }
        ],
    ],
]);

VCR::turnOn();

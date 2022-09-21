<?php

use allejo\VCR\VCRCleaner;
use VCR\VCR;

if (!file_exists('test/cassettes')) {
    mkdir('test/cassettes', 0755, true);
}

VCR::configure()->setCassettePath('test/cassettes')
    ->setStorage('yaml')
    ->setMode('once');

$scrubbedString = '<REDACTED>';
$scrubbedArray = []; // In PHP, this could be either an array or object

define('RESPONSE_BODY_SCRUBBERS', [
    ['api_keys', $scrubbedArray],
    ['client_ip', $scrubbedString],
    ['credentials', $scrubbedArray],
    ['email', $scrubbedString],
    ['fields', $scrubbedArray], // credential fields
    ['key', $scrubbedString],
    ['keys', $scrubbedArray],
    ['phone_number', $scrubbedString],
    ['phone', $scrubbedString],
    ['test_credentials', $scrubbedArray],
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
        'bodyScrubbers' => [
            function ($responseBody) {
                $responseBodyJson = json_decode($responseBody, true);
                $responseBodyEncoded = scrubCassette($responseBodyJson);

                // Re-encode the data so we can properly store it in the cassette
                return json_encode($responseBodyEncoded);
            }
        ],
    ],
]);

/**
 * Scrub sensitive information from cassette files prior to persisting on disk.
 *
 * @param mixed $responseBodyJson
 * @return mixed
 */
function scrubCassette($responseBodyJson)
{
    if (isset($responseBodyJson)) {
        foreach (RESPONSE_BODY_SCRUBBERS as $scrubber) {
            $key = $scrubber[0];
            $replacement = $scrubber[1];

            // Root-level list scrubbing
            if (isArrayAssociative($responseBodyJson)) {
                foreach ($responseBodyJson as $index => $element) {
                    if (is_array($element)) {
                        if (array_key_exists($key, $element)) {
                            $responseBodyJson[$index][$key] = $replacement;
                        }
                    }
                }
            } else {
                // Root-level key scrubbing
                if (array_key_exists($key, $responseBodyJson)) {
                    $responseBodyJson[$key] = $replacement;
                } else {
                    // Recursively scrub each element of a response
                    foreach ($responseBodyJson as $index => $element) {
                        if (is_array($element)) {
                            $responseBodyJson[$index] = scrubCassette($element);
                        }
                    }
                }
            }
        }
    }

    return $responseBodyJson;
}

/**
 * Determines if an array is associative (eg: JSON) or not.
 *
 * PHP treats JSON objects (associative arrays) and lists (sequential arrays) as the
 * same thing (array), so one can use this function to determine what kind of array something is.
 *
 * @param array $array
 * @return boolean
 */
function isArrayAssociative($array)
{
    return array_keys($array) == range(0, count($array) - 1);
}

VCR::turnOn();

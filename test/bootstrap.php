<?php

use allejo\VCR\VCRCleaner;
use EasyPost\Util\InternalUtil;
use VCR\VCR;

// Must be an absolute path, otherwise PHP VCR segfaults: https://github.com/php-vcr/php-vcr/issues/373
$cassetteDir = dirname(__FILE__) . '/cassettes';

if (!file_exists($cassetteDir)) {
    mkdir($cassetteDir, 0755, true);
}

VCR::configure()->setCassettePath($cassetteDir)
    ->setStorage('yaml')
    ->setMode('once')
    ->setWhiteList(['vendor/guzzle']);

$scrubbedString = '<REDACTED>';
$scrubbedArray = []; // In PHP, this could be either an array or object

define('RESPONSE_BODY_SCRUBBERS', [
    ['client_ip', $scrubbedString],
    ['credentials', $scrubbedArray],
    ['email', $scrubbedString],
    ['fields', $scrubbedArray],
    ['key', $scrubbedString],
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
 * @param mixed $data
 * @return mixed
 */
function scrubCassette($data)
{
    if (isset($data)) {
        foreach (RESPONSE_BODY_SCRUBBERS as $scrubber) {
            $key = $scrubber[0];
            $replacement = $scrubber[1];

            // Root-level list scrubbing
            if (InternalUtil::isList($data)) {
                foreach ($data as $index => $item) {
                    if (is_array($index)) {
                        if (is_array($item)) {
                            if (array_key_exists($key, $item)) {
                                $data[$index][$key] = $replacement;
                            }
                        }
                    }
                }
            } else {
                // Root-level key scrubbing
                if (is_array($data)) {
                    if (array_key_exists($key, $data)) {
                        $data[$key] = $replacement;
                    } else {
                        // Nested scrubbing
                        foreach ($data as $index => $item) {
                            if (is_array($item)) {
                                if (InternalUtil::isList($item)) {
                                    foreach ($item as $nestedIndex => $nestedItem) {
                                        $data[$index][$nestedIndex] = scrubCassette($nestedItem);
                                    }
                                } elseif (!InternalUtil::isList($item)) {
                                    $data[$index] = scrubCassette($item);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    return $data;
}

VCR::turnOn();

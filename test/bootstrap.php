<?php

use VCR\VCR;
use allejo\VCR\VCRCleaner;

if (!file_exists('test/cassettes')) {
    mkdir('test/cassettes', 0755, true);
}

VCR::configure()->setCassettePath('test/cassettes')
    ->setStorage('yaml')
    ->setMode('once');

VCRCleaner::enable(array(
    'request' => array(
        'ignoreHeaders' => array(
            'Authorization',        // Ignore credentials
            'User-Agent',           // Ignore varying version numbers between builds
            'X-Client-User-Agent',  // Ignore varying user agents between machines
        ),
    ),
));

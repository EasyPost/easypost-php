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
            'Authorization',
            'X-Client-User-Agent',
        ),
    ),
));

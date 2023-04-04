<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use EasyPost\Exception\General\EasyPostException;
use EasyPost\Exception\General\MissingParameterException;

class EasyPostClientTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test setting and getting the API key for different EasyPostClients.
     */
    public function testApiKey()
    {
        $apiKey1 = '123';
        $client1 = new EasyPostClient($apiKey1);
        $clientApiKey1 = $client1->getApiKey();

        $apiKey2 = '456';
        $client2 = new EasyPostClient($apiKey2);
        $clientApiKey2 = $client2->getApiKey();

        $this->assertEquals($apiKey1, $clientApiKey1);
        $this->assertEquals($apiKey2, $clientApiKey2);
    }

    /**
     * Test setting and getting the API base.
     */
    public function testApiBase()
    {
        $apiBase = 'http://example.com';

        $client = new EasyPostClient(getenv('EASYPOST_TEST_API_KEY'), 60.0, $apiBase);
        $clientApiBase = $client->getApiBase();

        $this->assertEquals($apiBase, $clientApiBase);
    }

    /**
     * Test setting and getting the timeout.
     */
    public function testTimeout()
    {
        $timeout = 1.0;

        $client = new EasyPostClient(getenv('EASYPOST_TEST_API_KEY'), $timeout);
        $clientTimeout = $client->getTimeout();

        $this->assertEquals($timeout, $clientTimeout);
    }

    /**
     * Test setting and getting the API key for different EasyPostClients.
     */
    public function testNoApiKey()
    {
        try {
            new EasyPostClient(null);
        } catch (MissingParameterException $error) {
            $this->assertEquals(
                'No API key provided. See https://www.easypost.com/docs for details, or contact support@easypost.com for assistance.', // phpcs:ignore
                $error->getMessage()
            );
        }
    }

    /**
     * Test invalid property (service) called on an EasyPostClient.
     */
    public function testInvalidServiceProperty()
    {
        try {
            $client = new EasyPostClient('123');
            $client->invalidProperty;
        } catch (EasyPostException $error) {
            $this->assertEquals(
                'EasyPost Notice: Undefined property of EasyPostClient instance: invalidProperty',
                $error->getMessage()
            );
        }
    }
}

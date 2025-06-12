<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use EasyPost\Exception\General\EasyPostException;
use PHPUnit\Framework\TestCase;

class EasyPostClientTest extends TestCase
{
    /**
     * Test setting and getting the API key for different EasyPostClients.
     */
    public function testApiKey(): void
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
    public function testApiBase(): void
    {
        $apiBase = 'http://example.com';

        $client = new EasyPostClient((string)getenv('EASYPOST_TEST_API_KEY'), 60.0, $apiBase);
        $clientApiBase = $client->getApiBase();

        $this->assertEquals($apiBase, $clientApiBase);
    }

    /**
     * Test setting and getting the timeout.
     */
    public function testTimeout(): void
    {
        $timeout = 1.0;

        $client = new EasyPostClient((string)getenv('EASYPOST_TEST_API_KEY'), $timeout);
        $clientTimeout = $client->getTimeout();

        $this->assertEquals($timeout, $clientTimeout);
    }

    /**
     * Test invalid property (service) called on an EasyPostClient.
     */
    public function testInvalidServiceProperty(): void
    {
        try {
            $client = new EasyPostClient('123');
            $client->invalidProperty; // @phpstan-ignore-line
        } catch (EasyPostException $error) {
            $this->assertEquals(
                'EasyPost Notice: Undefined property of EasyPostClient instance: invalidProperty',
                $error->getMessage()
            );
        }
    }
}

<?php

namespace EasyPost\Test;

use EasyPost\EasyPost;

class EasyPostTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Cleanup the testing environment once finished.
     */
    public static function tearDownAfterClass(): void
    {
        // Set all the defaults again so other tests don't fail
        EasyPost::setApiBase('https://api.easypost.com/v2');
        EasyPost::setApiVersion('2');
        EasyPost::setTimeout(60.0);
    }

    /**
     * Test setting and getting the API key.
     */
    public function testApiKey()
    {
        EasyPost::setApiKey(getenv('EASYPOST_TEST_API_KEY'));
        $apiKey = EasyPost::getApiKey();

        $this->assertNotNull($apiKey);
    }

    /**
     * Test setting and getting the API base.
     */
    public function testApiBase()
    {
        $testBase = 'http://example.com';

        EasyPost::setApiBase($testBase);
        $apiBase = EasyPost::getApiBase();

        $this->assertEquals($testBase, $apiBase);
    }

    /**
     * Test setting and getting the API version.
     */
    public function testApiVersion()
    {
        $testVersion = '100';

        EasyPost::setApiVersion($testVersion);
        $apiVersion = EasyPost::getApiVersion();

        $this->assertEquals($testVersion, $apiVersion);
    }

    /**
     * Test setting and getting the timeout.
     */
    public function testTimeout()
    {
        $testTimeout = 1.0;

        EasyPost::setTimeout($testTimeout);
        $connectionTimeout = EasyPost::getTimeout();

        $this->assertEquals($testTimeout, $connectionTimeout);
    }
}

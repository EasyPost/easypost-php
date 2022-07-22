<?php

namespace EasyPost\Test;

use EasyPost\EasyPost;

class EasyPostTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Cleanup the testing environment once finished.
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        // Set all the defaults again so other tests don't fail
        EasyPost::setApiBase('https://api.easypost.com/v2');
        EasyPost::setApiVersion('2');
        EasyPost::setConnectTimeout(30000);
        EasyPost::setResponseTimeout(60000);
    }

    /**
     * Test setting and getting the API key.
     *
     * @return void
     */
    public function testApiKey()
    {
        EasyPost::setApiKey(getenv('EASYPOST_TEST_API_KEY'));
        $apiKey = EasyPost::getApiKey();

        $this->assertNotNull($apiKey);
    }

    /**
     * Test setting and getting the API base.
     *
     * @return void
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
     *
     * @return void
     */
    public function testApiVersion()
    {
        $testVersion = '100';

        EasyPost::setApiVersion($testVersion);
        $apiVersion = EasyPost::getApiVersion();

        $this->assertEquals($testVersion, $apiVersion);
    }

    /**
     * Test setting and getting the connection timeout.
     *
     * @return void
     */
    public function testConnectionTimeout()
    {
        $testTimeout = '1';

        EasyPost::setConnectTimeout($testTimeout);
        $connectionTimeout = EasyPost::getConnectTimeout();

        $this->assertEquals($testTimeout, $connectionTimeout);
    }

    /**
     * Test setting and getting the request timeout.
     *
     * @return void
     */
    public function testRequestTimeout()
    {
        $testTimeout = '1';

        EasyPost::setResponseTimeout($testTimeout);
        $connectionTimeout = EasyPost::getResponseTimeout();

        $this->assertEquals($testTimeout, $connectionTimeout);
    }
}
